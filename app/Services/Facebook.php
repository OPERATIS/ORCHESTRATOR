<?php

namespace App\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Facebook
{
    private $client;

    public function __construct()
    {
        $options = [
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
        ];
        $this->client = new Client($options);
    }

    /**
     * @param $id
     * @param $accessToken
     * @return mixed
     * @throws GuzzleException
     */
    public function getPage($id, $accessToken)
    {
        $response = $this->client
            ->get("https://graph.facebook.com/v17.0/{$id}", [
                'query' => [
                    'access_token' => $accessToken,
                    'fields' => 'id,adaccounts'
                ]
            ]);

        return json_decode($response->getBody());
    }

    /**
     * @param $id
     * @param $accessToken
     * @param string $since
     * @param string $until
     * @param string|null $after
     * @return mixed
     * @throws GuzzleException
     */
    public function getInsights($id, $accessToken, string $since, string $until, string $after = null)
    {
        $fields = [
            'clicks',
            'impressions',
            'spend',
            'unique_clicks',
            'ad_id'
        ];

        $query = [
            'access_token' => $accessToken,
            'fields' => implode(',', $fields),
            'time_range[since]' => Carbon::parse($since)->toDateString(),
            'time_range[until]' => Carbon::parse($until)->toDateString(),
        ];

        if (!empty($after)) {
            $query['after'] = $after;
        }

        $response = $this->client
            ->get("https://graph.facebook.com/v17.0/{$id}/insights", [
                'query' => $query
            ]);

        return json_decode($response->getBody());
    }
}
