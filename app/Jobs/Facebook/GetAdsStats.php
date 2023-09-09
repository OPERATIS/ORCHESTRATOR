<?php

namespace App\Jobs\Facebook;

use App\Models\AdsStat;
use App\Models\Connect;
use App\Services\Facebook;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetAdsStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $connect = null;
    protected $startPeriod = null;
    protected $endPeriod = null;

    public function __construct(Connect $connect, $startPeriod, $endPeriod)
    {
        $this->connect = $connect;
        $this->startPeriod = $startPeriod;
        $this->endPeriod = $endPeriod;
    }

    public function handle(): void
    {
        $facebook = $this->connect;

        /** @var Facebook $facebookService */
        $facebookService = app()->make(Facebook::class);

        // Search accounts
        $status = false;
        $attempt = 0;
        do {
            try {
                $info = $facebookService->getPage($facebook->app_user_id, $facebook->access_token);
                $status = true;
                $attempt = 0;
            } catch (\Exception $exception) {
                $attempt++;
            }
        } while (!$status && $attempt < 3);

        $adIds = [];
        if (isset($info) && isset($info->adaccounts)) {
            foreach ($info->adaccounts->data as $account) {
                $adIds[] = $account->id;
            }
        }

        $after = null;
        $stats = [];
        foreach ($adIds as $adId) {
            do {
                try {
                    $info = $facebookService->getInsights($adId, $facebook->access_token, Carbon::now()->toDateString(), Carbon::now()->toDateString(), $after);
                    if ($info->paging->next ?? null) {
                        $after = $info->paging->cursors->after;
                    } else {
                        $after = null;
                    }

                    foreach ($info->data as $record) {
                        $stats[] = [
                            'connect_id' => $this->connect->id,
                            'clicks' => $record->clicks,
                            'impressions' => $record->impressions,
                            'spend' => $record->spend,
                            'unique_clicks' => $record->unique_clicks,
                            'ad_id' => $record->ad_id,
                            'start_period' => $this->startPeriod,
                            'end_period' => $this->endPeriod,
                            'created_at' => $this->endPeriod,
                            'updated_at' => $this->endPeriod
                        ];
                    }

                    $attempt = 0;
                } catch (\Exception $exception) {
                    $attempt++;
                }
            } while (!empty($after) && $attempt < 3);
        }

        AdsStat::insert($stats);
    }
}