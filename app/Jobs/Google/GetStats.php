<?php

namespace App\Jobs\Google;

use App\Models\Connect;
use App\Models\GaProfile;
use App\Models\GaStat;
use Carbon\Carbon;
use Google\Client;
use Google\Exception;
use Google\Service\Analytics;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Log as LogService;

class GetStats implements ShouldQueue
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
        $logService = new LogService('google', $this->connect->id);

        $client = new Client();
        $client->setAuthConfig(config_path() . '/googleCredentials.json');

        // Refresh token
        $actualToken = Carbon::parse($google->updated_at)->timestamp + $google->expires_in - 300 > Carbon::now()->timestamp;
        $info = $client->refreshToken($google->refresh_token);
        if (!$actualToken) {
            $attempt = 0;
            do {
                try {
                    $info = $client->refreshToken($google->refresh_token);
                    $attempt = 0;
                    $actualToken = true;
                    $logService->addSuccess('refreshToken');
                } catch (\Exception $exception) {
                    $attempt++;
                    $logService->addError('refreshToken', $exception->getMessage());
                }
            } while (!$actualToken && $attempt < 3);

            $actualToken = false;
            if (!empty($info) && !isset($info['error'])) {
                $google->access_token = $info['access_token'];
                $google->expires_in = $info['expires_in'];
                $google->refresh_token = $info['refresh_token'];
                $google->save();
                $actualToken = true;
            }
        }

        if ($actualToken) {
            $client->setAccessToken($google->access_token);
            $analytics = new Analytics($client);

            $profiles = GaProfile::where('connect_id', $google->id)
                ->where('actual', 1)
                ->get();

            foreach ($profiles as $profile) {
                $profileId = $profile->profile_id;
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
                                Carbon::parse($this->startPeriod)->setTimezone($profile->timezone)->toDateString(),
                                Carbon::parse($this->endPeriod)->setTimezone($profile->timezone)->toDateString(),
                                implode(',', $all),
                                [
                                    'include-empty-rows' => true
                                ]
                            );
                            $attempt = 0;
                            $status = true;
                            $logService->addSuccess('get', $profileId);
                        } catch (\Exception $exception) {
                            $attempt++;
                            $logService->addError('get', $exception->getMessage(), $profileId);
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
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                            'unique_table_id' => $profileId
                        ];
                    }

                    GaStat::insert($stats);
                }
            }
        }
    }
}
