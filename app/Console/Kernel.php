<?php

namespace App\Console;

use App\Models\Metric;
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
        $schedule->command('shopify:get-orders demo')->everyFiveMinutes();

        // All stats by full day
        $schedule->command('facebook:get-stats')->everyFiveMinutes();
        $schedule->command('facebook:get-stats')->dailyAt('23:59');
        $schedule->command('facebook:get-stats demo')->everyFiveMinutes();
        // All stats by full day
        $schedule->command('google:get-stats')->everyFiveMinutes();
        $schedule->command('google:get-stats')->dailyAt('23:59');
        $schedule->command('google:get-stats demo')->everyFiveMinutes();

        // For example 13:15
        // Convert stats to 5 minutes 13:05-13:10
        $schedule->command('google:aggregation-stats')->everyFiveMinutes();
        // Convert stats to 5 minutes 13:05-13:10
        $schedule->command('facebook:aggregation-stats')->everyFiveMinutes();

        // For example 13:15
        // Save metrics by period 12:00-13:00
        // Init search alerts and then init save analyzes
        $schedule->command("save-metrics '" . Metric::PERIOD_HOUR . "'")->cron('15 * * * *');
        // For example 03.10.23 00:45
        // Save metrics by period 02.10.23-03.10.23
        // Init search recommendations and then init save analyzes
        $schedule->command("save-metrics '" . Metric::PERIOD_DAY . "'")->dailyAt('00:45');
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
