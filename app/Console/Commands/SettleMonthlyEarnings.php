<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Services\RevenueSettlement;
use Throwable;
class SettleMonthlyEarnings extends Command {
    protected $signature='earnings:settle {--month= : Completed month YYYY-MM} {--pretend : Calculate without saving} {--force : Unsupported; historical credits are immutable}';
    protected $description='Prepare monthly revenue for admin review. Never credits wallets automatically.';
    public function handle(): int {
        try {
            if($this->option('force'))throw new \RuntimeException('Re-settlement is disabled. Historical credits cannot be overwritten.');
            $month=$this->option('month')?:now()->subMonthNoOverflow()->format('Y-m');
            $service=app(RevenueSettlement::class);
            $s=$this->option('pretend')?$service->calculate($month):$service->prepare($month);
            $this->line('  Subscription revenue:          '.(($s['subscription_cents']+$s['subscription_adjustment_cents'])/100));
            $this->line('  Eligible streams (approved):    '.$s['total_streams']);
            foreach($s['blockers'] as $blocker)$this->warn($blocker);
            $this->info($this->option('pretend')?'Preview only. No changes written.':'Review prepared. Admin approval is required before wallet credits.');return 0;
        }catch(Throwable $e){$this->error($e->getMessage());return 1;}
    }
}
