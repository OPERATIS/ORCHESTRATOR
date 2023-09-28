<?php

namespace App\Http\Controllers;

use App\Models\MeUser;
use App\Models\WaUser;
use App\Services\Facebook;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class WebhooksController extends Controller
{
    public function whatsapp(Request $request)
    {
        if ($request->isJson()) {
            $response = $request->all();

            // Check first message
            if (isset($response['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'])) {
                $message = $response['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
                $userId = str_replace('Start to notifications #', '', $message);
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
                        // TODO add text
                        $facebook->sendWaMessage($waId, null, 'add text');
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
                $userId = str_replace('Start to notifications #', '', $message);
                if (is_numeric($userId)) {
                    $meUser = MeUser::updateOrCreate([
                        'user_id' => $userId,
                        'psid' => $psid,
                    ]);

                    if (Carbon::parse($meUser->created_at)->seconds(0)->toDateTimeString() === Carbon::now()->seconds(0)->toDateTimeString()) {
                        $facebook = new Facebook();
                        // TODO add text
                        $facebook->sendMeMessage('Add text', $meUser->psid);
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
}
