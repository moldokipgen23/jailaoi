<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\OperationsHealth;
class OperationsHealthCheck extends Command
{
    protected $signature='jailaoi:health-check';
    protected $description='Check operational readiness and record private admin alerts.';
    public function handle(): int
    {
        $health=app(OperationsHealth::class);$report=$health->inspect();$health->save($report);
        if($report['issue_count'])Log::warning('Operational checks need attention',['issue_count'=>$report['issue_count']]);
        $this->info($report['issue_count'].' checks need attention. See admin operations health.');
        return $report['issue_count']?Command::FAILURE:Command::SUCCESS;
    }
}
