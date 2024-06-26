<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Telegram
{
    private $client;
    private $token;

    public function __construct()
    {
        $options = [
            'curl' => [
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            ],
        ];
        $this->client = new Client($options);
        $this->token = config('integrations.telegram.botToken');
    }

    /**
     * @param int $chatId
     * @param string $message
     * @return mixed
     * @throws GuzzleException
     */
    public function sendMessage(int $chatId, string $message)
    {
        $response = $this->client
            ->post('https://api.telegram.org/bot' . $this->token . '/sendMessage', [
                'query' => [
                    'chat_id' => $chatId,
                    'text' => $message,
                ]
            ]);

        return json_decode($response->getBody());
    }
}
