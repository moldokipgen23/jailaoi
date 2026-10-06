<?php

namespace App\Console\Commands;

use App\Models\ArtistEarning;
use App\Models\General_Setting;
use App\Models\MonetizationApplication;
use App\Models\WithdrawalRequest;
use App\Models\Transaction;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SettleMonthlyEarnings extends Command
{
    protected $signature = 'earnings:settle
        {--month= : Month to settle (YYYY-MM). Defaults to previous month.}
        {--pretend : Show calculations without writing anything}
        {--force : Allow re-settling an already-settled month}';

    protected $description = 'Settle the monthly revenue pool: distribute the artist share of subscription revenue (100% minus platform_cut_pct) based on each artist\'s stream share';

    public function handle(): int
    {
        $lockHeld = false;
        try {
            if (DB::getDriverName() === 'mysql') {
                $lockHeld = (int) DB::selectOne("SELECT GET_LOCK('jailaoi:earnings-settle', 0) AS acquired")->acquired === 1;
                if (!$lockHeld) {
                    $this->error('Another settlement is running. Please try again later.');
                    return 1;
                }
            }
            // Calculations, reversal, wallet credits and audit marker share one transaction.
            return DB::transaction(fn () => $this->settle());
        } catch (Exception $e) {
            $this->error('Settlement failed: ' . $e->getMessage());
            return 1;
        } finally {
            if ($lockHeld) DB::selectOne("SELECT RELEASE_LOCK('jailaoi:earnings-settle')");
        }
    }

    private function settle(): int
    {
            $month = $this->option('month') ?: now()->subMonth()->format('Y-m');
            $pretend = (bool) $this->option('pretend');
            $force = (bool) $this->option('force');

            // Validate month format
            if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/D', $month)) {
                $this->error("Invalid month format. Use YYYY-MM (e.g. 2026-05)");
                return 1;
            }

            $this->info("=== Earnings Settlement for {$month} ===");

            // Check if already settled
            $existing = DB::table('tbl_earnings_settlements')->where('month', $month)->first();
            if ($existing) {
                if ($force) {
                    $this->warn("Month {$month} already settled. --force used, will re-settle.");
                } else {
                    $this->error("Month {$month} already settled. Use --force to re-settle.");
                    return 1;
                }
            }

            $monthStart = "{$month}-01 00:00:00";
            $monthEnd   = date('Y-m-d H:i:s', strtotime($monthStart . ' +1 month'));

            // --- Step 1: Calculate subscription revenue ---
            $totalRevenue = (float) Transaction::where('created_at', '>=', $monthStart)
                ->where('created_at', '<', $monthEnd)
                ->sum(DB::raw('CAST(price AS DECIMAL(12,2))'));

            $this->line("  Subscription revenue:          {$totalRevenue}");

            // --- Step 2: Apply platform cut ---
            $platformCutPct = (float) (General_Setting::where('key', 'platform_cut_pct')->value('value') ?? 30);
            if ($platformCutPct < 0 || $platformCutPct > 100) throw new \RuntimeException('Platform cut must be between 0 and 100 percent.');
            $platformCut = round($totalRevenue * ($platformCutPct / 100), 2);
            $poolAmount  = round($totalRevenue - $platformCut, 2);

            $this->line("  Platform cut ({$platformCutPct}%):           {$platformCut}");
            $artistPct = 100 - $platformCutPct;
            $this->line("  Artist pool ({$artistPct}%):             {$poolAmount}");

            // --- Step 3: Count eligible streams (approved artists, unsettled) ---
            $totalStreams = DB::table('tbl_artist_earnings as ae')
                ->join('tbl_monetization_applications as ma', 'ma.artist_id', '=', 'ae.artist_id')
                ->where('ma.status', 'approved')
                ->where('ae.created_at', '>=', $monthStart)
                ->where('ae.created_at', '<', $monthEnd)
                ->where(function ($query) use ($force, $existing, $month) {
                    $query->whereNull('ae.settled_month');
                    if ($force && $existing) $query->orWhere('ae.settled_month', $month);
                })
                ->count();

            $this->line("  Eligible streams (approved):    {$totalStreams}");

            $ratePerStream = $totalStreams > 0 ? round(max(0, $poolAmount) / $totalStreams, 6) : 0;
            $this->line("  Rate per stream:               {$ratePerStream}");

            // --- Step 4: Preview or execute ---
            if ($pretend) {
                $this->warn("--pretend mode: no changes written.");
                $this->table(
                    ['Metric', 'Value'],
                    [
                        ['Month', $month],
                        ['Total Revenue', $totalRevenue],
                        ['Platform Cut', $platformCut],
                        ['Pool Amount', $poolAmount],
                        ['Total Streams', $totalStreams],
                        ['Rate per Stream', $ratePerStream],
                    ]
                );
                return 0;
            }

            if ($force && $existing) {
                $this->warn("Reverting previous settlement for {$month} (inside transaction)...");
            }

            // --- Step 5 & 6: Revert (if --force) + execute settlement atomically ---
            DB::transaction(function () use ($month, $monthStart, $monthEnd, $ratePerStream, $force, $existing) {
                // If re-settling, revert previous wallet credits first (inside same transaction)
                if ($force && $existing) {
                    DB::update("
                        UPDATE tbl_artist a
                        INNER JOIN (
                            SELECT artist_id, SUM(amount) as earned
                            FROM tbl_artist_earnings
                            WHERE settled_month = ? AND amount > 0
                            GROUP BY artist_id
                        ) e ON e.artist_id = a.id
                        SET a.wallet_balance = GREATEST(0, a.wallet_balance - e.earned)
                    ", [$month]);
                    DB::table('tbl_artist_earnings')
                        ->where('settled_month', $month)
                        ->update(['settled_month' => null, 'amount' => 0]);
                }

                // Update approved artists: backfill amount and mark settled
                DB::update("
                    UPDATE tbl_artist_earnings ae
                    JOIN tbl_monetization_applications ma ON ma.artist_id = ae.artist_id
                    SET ae.amount = ?, ae.settled_month = ?
                    WHERE ma.status = 'approved'
                      AND ae.created_at >= ?
                      AND ae.created_at < ?
                      AND ae.settled_month IS NULL
                ", [$ratePerStream, $month, $monthStart, $monthEnd]);

                // Mark non-approved artist plays as settled (amount stays 0)
                DB::update("
                    UPDATE tbl_artist_earnings ae
                    SET ae.settled_month = ?
                    WHERE ae.settled_month IS NULL
                      AND ae.created_at >= ?
                      AND ae.created_at < ?
                      AND ae.artist_id NOT IN (
                          SELECT artist_id FROM tbl_monetization_applications WHERE status = 'approved'
                      )
                ", [$month, $monthStart, $monthEnd]);

                // Credit each artist's wallet balance with their settled earnings
                DB::update("
                    UPDATE tbl_artist a
                    INNER JOIN (
                        SELECT artist_id, SUM(amount) as earned
                        FROM tbl_artist_earnings
                        WHERE settled_month = ?
                          AND amount > 0
                        GROUP BY artist_id
                    ) e ON e.artist_id = a.id
                    SET a.wallet_balance = a.wallet_balance + e.earned
                ", [$month]);
            });

            // --- Step 6: Record settlement audit ---
            $this->recordSettlement($month, $totalRevenue, $platformCut, $poolAmount, $totalStreams, $ratePerStream);

            $this->info("✓ Settlement for {$month} complete.");
            $this->line("  {$totalStreams} streams × {$ratePerStream} = {$poolAmount} distributed.");
            $this->line("  Platform retained: {$platformCut}");

            return 0;
    }

    private function recordSettlement(string $month, float $totalRevenue, float $platformCut, float $poolAmount, int $totalStreams, float $ratePerStream): void
    {
        // Remove old record if force re-settle
        DB::table('tbl_earnings_settlements')->where('month', $month)->delete();

        DB::table('tbl_earnings_settlements')->insert([
            'month'            => $month,
            'total_revenue'    => $totalRevenue,
            'platform_cut'     => $platformCut,
            'pool_amount'      => $poolAmount,
            'total_streams'    => $totalStreams,
            'rate_per_stream'  => $ratePerStream,
            'additional_revenue' => 0,
            'settled_at'       => now(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }
}
