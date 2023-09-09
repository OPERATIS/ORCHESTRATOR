<?php

namespace App\Jobs\Google;

use App\Models\Connect;
use App\Models\GaStat;
use App\Services\Google;
use Carbon\Carbon;
use Google\Client;
use Google\Exception;
use Google\Service\Analytics;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GetGaStats implements ShouldQueue
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

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $google = $this->connect;

        $client = new Client();
        $client->setAuthConfig(config_path() . '/googleCredentials.json');

        // Refresh token
        $actualToken = Carbon::parse($google->updated_at)->timestamp + $google->expires_in - 300 < Carbon::now()->timestamp;
        if (!$actualToken) {
            $attempt = 0;
            do {
                try {
                    $info = $client->refreshToken($google->refresh_token);
                    $attempt = 0;
                    $actualToken = true;
                } catch (\Exception $exception) {
                    $attempt++;
                }
            } while (!$actualToken && $attempt < 3);

            if (!empty($info)) {
                $google->access_token = $info['access_token'];
                $google->expires_in = $info['expires_in'];
                $google->refresh_token = $info['refresh_token'];
                $google->save();
            }
        }

        if ($actualToken) {
            $client->setAccessToken($google->access_token);
            $analytics = new Analytics($client);
            $status = false;
            $attempt = 0;
            do {
                try {
                    $profileId = Google::getFirstProfileId($analytics, 'ESM.one');
                    $status = true;
                    $attempt = 0;
                } catch (\Exception $exception) {
                    $attempt++;
                }
            } while (!$status && $attempt < 3);

            if (!empty($profileId)) {
                $all = [
                    'ga:impressions',
                    'ga:pageviews',
                    'ga:uniquePageviews',
                    'ga:adClicks',
                    'ga:adCost',
                ];

                $status = false;
                $attempt = 0;
                do {
                    try {
                        $results = $analytics->data_ga->get(
                            'ga:' . $profileId,
                            Carbon::parse($this->startPeriod)->toDateString(),
                            Carbon::parse($this->endPeriod)->toDateString(),
                            implode(',', $all),
                            [
                                'include-empty-rows' => true
                            ]
                        );
                        $attempt = 0;
                        $status = true;
                    } catch (\Exception $exception) {
                        $attempt++;
                    }
                } while (!$status && $attempt < 3);

                $stats = [];
                if (!empty($results) && count($results->getRows()) > 0) {
                    $rows = $results->getRows();
                    $stats = [
                        'connect_id' => $google->id,
                        'impressions' => $rows[0][0],
                        'pageviews' => $rows[0][1],
                        'unique_pageviews' => $rows[0][2],
                        'ad_clicks' => $rows[0][3],
                        'ad_cost' => $rows[0][4],
                        'start_period' => $this->startPeriod,
                        'end_period' => $this->endPeriod,
                        'created_at' => $this->endPeriod,
                        'updated_at' => $this->endPeriod,
                        'unique_table_id' => $profileId
                    ];
                }

                GaStat::insert($stats);
            }
        }
    }
}
