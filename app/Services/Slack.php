<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Slack
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
     * @param string $token
     * @param string $channel
     * @param string $text
     * @return mixed
     * @throws GuzzleException
     */
    public function chatPostMessage(string $token, string $channel, string $text)
    {
        $response = $this->client
            ->post('https://slack.com/api/chat.postMessage', [
                    'headers' => [
                        'Authorization' => "Bearer {$token}",
                    ],
                    'form_params' => [
                        'channel' => $channel,
                        'text' => $text,
                    ]]
            );

        return json_decode($response->getBody());
    }

    /**
     * @param string $code
     * @return mixed
     * @throws GuzzleException
     */
    public function authV2Access(string $code)
    {
        $response = $this->client
            ->post('https://slack.com/api/oauth.v2.access', [
                    'auth' => [
                        config('services.slack.client_id'),
                        config('services.slack.client_secret')
                    ],
                    'form_params' => [
                        'code' => $code,
                    ]
                ]
            );

        return json_decode($response->getBody());
    }
}
