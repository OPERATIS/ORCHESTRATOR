<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Metric;
use App\Models\User;
use GuzzleHttp\Exception\GuzzleException;

class Notifications
{
    /**
     * @param Alert $alert
     * @return string|null
     */
    public static function getMessageFromAlert(Alert $alert): ?string
    {
        $message = null;
        if ($alert->result === Alert::INCREASED) {
            $message = 'There is anomalous increase in ' . strtoupper($alert->metric) . ' (' . Metric::NAMES[$alert->metric] . ') metric value during last hour.';
        } elseif ($alert->result === Alert::DECREASED) {
            $message = 'There is anomalous decrease in ' . strtoupper($alert->metric) . ' (' . Metric::NAMES[$alert->metric] . ') metric value during last hour.';
        }

        return $message;
    }

    /**
     * @param int $userId
     * @param Alert $alert
     */
    public static function sendAlert(int $userId, Alert $alert)
    {
        $user = User::where('id', $userId)
            ->with(['telegrams', 'whatsApps', 'messengers', 'slacks'])
            ->first();

        if ($user) {
            // Prepare message
            $message = $alert->getMessage();
            if ($message) {
                // Send telegram
                $telegramService = new Telegram();
                if ($user->telegrams) {
                    foreach ($user->telegrams as $telegram) {
                        try {
                            $telegramService->sendMessage($telegram->chat_id, $message);
                        } catch (\Exception | GuzzleException $exception) {

                        }
                    }
                }

                // Send whatsapp
                $facebookService = new Facebook();
                if ($user->whatsApps) {
                    foreach ($user->whatsApps as $whatsApp) {
                        try {
                            $facebookService->sendWaMessage($whatsApp->wa_id, $message);
                        } catch (\Exception | GuzzleException $exception) {

                        }
                    }
                }

                // Send messenger
                if ($user->messengers) {
                    foreach ($user->messengers as $messenger) {
                        try {
                            $facebookService->sendMeMessage($messenger->wa_id, $message);
                        } catch (\Exception | GuzzleException $exception) {

                        }
                    }
                }

                // Send slack
                $slackService = new Slack();
                if ($user->slacks) {
                    foreach ($user->slacks as $slack) {
                        try {
                            $slackService->chatPostMessage($slack->access_token, $slack->authed_user_id, $message);
                        } catch (\Exception | GuzzleException $exception) {

                        }
                    }
                }
            }
        }
    }
}
