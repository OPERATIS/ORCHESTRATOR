<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        // For example 13:15
        // All orders by period 13:10-13:15
        $schedule->command('shopify:get-orders')->everyFiveMinutes();
        // All stats by full day
        $schedule->command('facebook:get-stats')->everyFiveMinutes();
        // All stats by full day
        $schedule->command('google:get-stats')->everyFiveMinutes();

        // For example 13:15
        // Convert stats to 5 minutes 13:05-13:10
        $schedule->command('google:aggregation-stats')->everyFiveMinutes();
        // Convert stats to 5 minutes 13:05-13:10
        $schedule->command('facebook:aggregation-stats')->everyFiveMinutes();

        // For example 13:15
        // Save metrics by period 12:00-13:00
        // Init search alerts and then init save analyzes
        $schedule->command('save-metrics')->cron('15 * * * *');

        // Integrations for notifications
        $schedule->command('telegram:get-updates')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
