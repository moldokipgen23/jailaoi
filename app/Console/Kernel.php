<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('app:update-summary')->weeklyOn(1, '00:00')->then(function () {
            Artisan::call('app:create-upload-batch-file');
        });
        $schedule->command('app:check-status')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('app:create-sections')->everyTenMinutes()->withoutOverlapping();
        $schedule->command('earnings:settle')->monthlyOn(5, '08:00')->withoutOverlapping();
        $schedule->command('app:send-subscription-notifications')->dailyAt('09:00')->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
