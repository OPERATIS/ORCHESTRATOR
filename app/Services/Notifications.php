<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\User;
use GuzzleHttp\Exception\GuzzleException;

class Notifications
{
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
            // TODO add text
            $message = 'Metric ' . $alert->metric . ' has ' . $alert->result;

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
