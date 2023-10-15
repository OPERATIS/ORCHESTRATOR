<?php

namespace App\Console\Commands;

use App\Models\AggregationFbStat;
use App\Models\AggregationGaStat;
use App\Models\Alert;
use App\Models\Analysis;
use App\Models\FbStat;
use App\Models\GaStat;
use App\Models\Metric;
use App\Models\User;
use App\Services\Demo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

class DemoStats extends Command
{
    protected $signature = 'demo:stats';

    public function handle(): bool
    {
        Demo::createUser();
        $this->info('User created');

        Demo::createConnections();
        $this->info('Connections created');

        // Generate old data
        $start = Carbon::parse('2023-08-01 00:00:00')->startOfMonth();
        $end = Carbon::now()->endOfDay();

        do {
            $endPeriod = $start->clone()->addMinutes(5)->toDateTimeString();
            $startPeriod = $start->toDateTimeString();
            $endPeriodAggregation = $start->clone()->toDateTimeString();
            $this->info('Period ' . $startPeriod . ' - ' . $endPeriod);

            $this->info('Start add gaStats');
            Demo::createGaStats($startPeriod, $endPeriod);

            $gaDemoIntegrationId = GaStat::DEMO_INTEGRATION_ID;
            $this->info('Remove google:aggregation-stats');
            AggregationGaStat::where('integration_id', $gaDemoIntegrationId)
                ->where('end_period', '=', $startPeriod)
                ->delete();

            $this->info('Start google:aggregation-stats');
            Artisan::call("google:aggregation-stats '{$endPeriodAggregation}' {$gaDemoIntegrationId}");

            $this->info('Start add fbStats');
            Demo::createFbStats($startPeriod, $endPeriod);

            $this->info('Remove facebook:aggregation-stats');
            $fbDemoIntegrationId = FbStat::DEMO_INTEGRATION_ID;
            AggregationFbStat::where('integration_id', $fbDemoIntegrationId)
                ->where('end_period', '=', $startPeriod)
                ->delete();

            $this->info('Start facebook:aggregation-stats');
            Artisan::call("facebook:aggregation-stats '{$endPeriodAggregation}' {$fbDemoIntegrationId}");

            $this->info('Start add orders');
            Demo::createOrders($startPeriod, $endPeriod);

            if (Carbon::parse($endPeriod)->minute === 15) {
                $this->info('Remove metrics');
                Metric::where('user_id', User::DEMO_ID)
                    ->where('end_period', Carbon::parse($endPeriod)->minutes(0)->toDateTimeString())
                    ->delete();

                $this->info('Remove alerts');
                Alert::where('user_id', User::DEMO_ID)
                    ->where('end_period', Carbon::parse($endPeriod)->minutes(0)->toDateTimeString())
                    ->delete();

                $this->info('Remove analyzes');
                Analysis::where('user_id', User::DEMO_ID)
                    ->where('end_period', Carbon::parse($endPeriod)->minutes(0)->toDateTimeString())
                    ->delete();

                $this->info('Start save metrics');

                $period = Metric::PERIOD_HOUR;
                Artisan::call("save-metrics {$period} '{$endPeriodAggregation}' demo");
            }

            if (Carbon::parse($endPeriod)->hour === 0 && Carbon::parse($endPeriod)->minute === 45) {
                $period = Metric::PERIOD_DAY;
                Artisan::call("save-metrics {$period} '{$endPeriodAggregation}' demo");
            }

            $start = $start->addMinutes(5);
        } while ($start < $end);

        return true;
    }
}
