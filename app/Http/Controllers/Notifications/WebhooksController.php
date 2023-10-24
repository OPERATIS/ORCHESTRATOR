<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\MeUser;
use App\Models\TgUser;
use App\Models\WaUser;
use App\Services\Facebook;
use App\Services\Notifications;
use App\Services\Telegram;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhooksController extends Controller
{
    public function whatsapp(Request $request)
    {
        if ($request->isJson()) {
            $response = $request->all();

            // Check first message
            if (isset($response['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'])) {
                $message = $response['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
                $userId = str_replace(Notifications::getMessageForInitSubscribe(), '', $message);
                $username = $response['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'];
                $waId = $response['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'];

                if (is_numeric($userId)) {
                    $waUser = WaUser::updateOrCreate([
                        'user_id' => $userId,
                        'wa_id' => $waId
                    ], [
                        'username' => $username
                    ]);

                    if (Carbon::parse($waUser->created_at)->seconds(0)->toDateTimeString() === Carbon::now()->seconds(0)->toDateTimeString()) {
                        $facebook = new Facebook();
                        $facebook->sendWaMessage($waId, null, Notifications::getMessageAfterSubscribe());
                    }
                }
            }

            return response('');
        } else {
            // Step validation
            $hubChallenge = $request->get('hub_challenge');

            return response($hubChallenge, 200, ['Content-Type' => 'text/plain']);
        }
    }

    /**
     * @throws GuzzleException
     */
    public function messenger(Request $request)
    {
        if ($request->isJson()) {
            $response = $request->all();
            if (isset($response['entry'][0]['messaging'][0]['message']['text'])) {
                $message = $response['entry'][0]['messaging'][0]['message']['text'];
                $psid = $response['entry'][0]['messaging'][0]['sender']['id'];
                $userId = str_replace(Notifications::getMessageForInitSubscribe(), '', $message);
                if (is_numeric($userId)) {
                    $meUser = MeUser::updateOrCreate([
                        'user_id' => $userId,
                        'psid' => $psid,
                    ]);

                    if (Carbon::parse($meUser->created_at)->seconds(0)->toDateTimeString() === Carbon::now()->seconds(0)->toDateTimeString()) {
                        $facebook = new Facebook();
                        $facebook->sendMeMessage($meUser->psid, Notifications::getMessageAfterSubscribe());
                    }
                }
            }

            return response('');
        } else {
            // Step validation
            $hubChallenge = $request->get('hub_challenge');

            return response($hubChallenge, 200, ['Content-Type' => 'text/plain']);
        }
    }

    /**
     * @param Request $request
     * @return Response
     * @throws GuzzleException
     */
    public function telegram(Request $request): Response
    {
        $response = $request->all();
        if (isset($response['message']['from']['id'])) {
            $fromId = $response['message']['from']['id'];
            $userId = base64_decode(str_replace('/start ', '', $response['message']['text']));
            $firstName = $response['message']['from']['first_name'];
            $username = $response['message']['from']['username'];

            if (is_numeric($userId)) {
                $tgUser = TgUser::updateOrCreate([
                    'user_id' => $userId,
                    'chat_id' => $fromId
                ], [
                    'first_name' => $firstName,
                    'username' => $username
                ]);

                if (Carbon::parse($tgUser->created_at)->seconds(0)->toDateTimeString() === Carbon::now()->seconds(0)->toDateTimeString()) {
                    $telegram = new Telegram();
                    $telegram->sendMessage($fromId, Notifications::getMessageAfterSubscribe());
                }
            }
        }

        return response('');
    }
}
