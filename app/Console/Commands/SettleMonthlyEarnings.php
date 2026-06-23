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

    protected $description = 'Settle the monthly revenue pool: distribute 55% of subscription revenue to artists based on stream share';

    public function handle(): int
    {
        try {
            $month = $this->option('month') ?: now()->subMonth()->format('Y-m');
            $pretend = (bool) $this->option('pretend');
            $force = (bool) $this->option('force');

            // Validate month format
            if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
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
            $totalRevenue = (float) Transaction::where('status', 1)
                ->where('created_at', '>=', $monthStart)
                ->where('created_at', '<', $monthEnd)
                ->sum(DB::raw('CAST(price AS DECIMAL(12,2))'));

            $this->line("  Subscription revenue:          {$totalRevenue}");

            // --- Step 2: Apply platform cut ---
            $platformCutPct = (float) (General_Setting::where('key', 'platform_cut_pct')->value('value') ?? 45);
            $platformCut = round($totalRevenue * ($platformCutPct / 100), 2);
            $poolAmount  = round($totalRevenue - $platformCut, 2);

            $this->line("  Platform cut ({$platformCutPct}%):           {$platformCut}");
            $this->line("  Artist pool (55%):              {$poolAmount}");

            if ($poolAmount <= 0) {
                $this->warn("Pool amount is zero or negative. Nothing to distribute.");
                if (!$pretend) {
                    $this->recordSettlement($month, $totalRevenue, $platformCut, $poolAmount, 0, 0);
                }
                return 0;
            }

            // --- Step 3: Count eligible streams (approved artists, unsettled) ---
            $totalStreams = DB::table('tbl_artist_earnings as ae')
                ->join('tbl_monetization_applications as ma', 'ma.artist_id', '=', 'ae.artist_id')
                ->where('ma.status', 'approved')
                ->where('ae.created_at', '>=', $monthStart)
                ->where('ae.created_at', '<', $monthEnd)
                ->whereNull('ae.settled_month')
                ->count();

            $this->line("  Eligible streams (approved):    {$totalStreams}");

            if ($totalStreams <= 0) {
                $this->warn("No eligible streams found for this month.");
                if (!$pretend) {
                    $this->recordSettlement($month, $totalRevenue, $platformCut, $poolAmount, 0, 0);
                }
                return 0;
            }

            $ratePerStream = round($poolAmount / $totalStreams, 6);
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

            // --- Step 5: Execute settlement in a transaction ---
            DB::transaction(function () use ($month, $monthStart, $monthEnd, $ratePerStream) {
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
            });

            // --- Step 6: Record settlement audit ---
            $this->recordSettlement($month, $totalRevenue, $platformCut, $poolAmount, $totalStreams, $ratePerStream);

            $this->info("✓ Settlement for {$month} complete.");
            $this->line("  {$totalStreams} streams × {$ratePerStream} = {$poolAmount} distributed.");
            $this->line("  Platform retained: {$platformCut}");

            return 0;
        } catch (Exception $e) {
            $this->error("Settlement failed: " . $e->getMessage());
            return 1;
        }
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
