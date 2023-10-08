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

    /**
     * @param $waId
     * @param string|null $template
     * @param string|null $text
     * @return mixed
     * @throws GuzzleException
     */
    public function sendWaMessage($waId, string $template = null, string $text = null)
    {
        $json = [
            'messaging_product' => 'whatsapp',
            'to' => $waId,
        ];

        if ($template) {
            $json['type'] = 'template';
            $json['template'] = [
                'name' => $template,
                'language' => [
                    'code' => 'en_US'
                ]
            ];
        } else {
            $json['type'] = 'text';
            $json['text'] = [
                'body' => $text
            ];
        }

        $response = $this->client->post('https://graph.facebook.com/v17.0/' . config('integrations.whatsapp.phoneNumberId') . '/messages', [
            'headers' => [
                'Authorization' => 'Bearer ' . config('integrations.whatsapp.accessToken'),
                'Content-Type' => 'application/json'
            ],
            'json' => $json
        ]);

        return json_decode($response->getBody());
    }

    /**
     * @throws GuzzleException
     */
    public function sendMeMessage($psid, string $text)
    {
        $response = $this->client
            ->post("https://graph.facebook.com/v17.0/" . config('integrations.messenger.pageId') . "/messages?recipient={'id':'$psid'}&messaging_type=RESPONSE&message={'text':'$text'}&access_token=" . config('integrations.messenger.pageAccessToken'));

        return json_decode($response->getBody());
    }
}
