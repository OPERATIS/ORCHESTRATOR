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
        $schedule->command('shopify:get-orders')->everyFiveMinutes();
        $schedule->command('facebook:get-stats')->everyFiveMinutes();
        $schedule->command('google:get-stats')->everyFiveMinutes();

        $schedule->command('google:aggregation-stats')->everyFiveMinutes();
        $schedule->command('facebook:aggregation-stats')->everyFiveMinutes();

        // after delay aggregation
        $schedule->command('save-metrics')->hourlyAt('25');
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
