<?php

namespace App\Http\Controllers;

use App\Models\MeUser;
use App\Models\WaUser;
use App\Services\Facebook;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class WebhooksController extends Controller
{
    /**
     * @throws GuzzleException
     */
    public function whatsapp(Request $request)
    {
        if ($request->isJson()) {
            $response = json_decode($request->json());
            $message = $response->entry[0]->changes[0]->value->messages[0]->text->body;
            $userId = str_replace('Start to notifications #', '', $message);
            $displayPhoneNumber = $response->entry[0]->changes[0]->value->metadata->display_phone_number;
            $phoneNumberId = $response->entry[0]->changes[0]->value->metadata->phone_number_id;
            $username = $response->entry[0]->changes[0]->value[0]->contacts[0]->profile->name;
            if (is_numeric($userId)) {
                $waUser = WaUser::updateOrCreate([
                    'user_id' => $userId,
                    'display_phone_number' => $displayPhoneNumber,
                    'phone_number_id' => $phoneNumberId,
                ], [
                    'username' => $username
                ]);

                if ($waUser->updated_at == $waUser->created_at) {
                    $facebook = new Facebook();
                    // TODO add text
                    $facebook->sendWaMessage('hello_world', $displayPhoneNumber);
                }
            }

            return response();
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
            $response = json_decode($request->json());
            $message = $response->message->text;
            $psid = $response->sender->id;
            $userId = str_replace('Start to notifications #', '', $message);
            if (is_numeric($userId)) {
                $meUser = MeUser::updateOrCreate([
                    'user_id' => $userId,
                    'psid' => $psid,
                ]);

                if ($meUser->updated_at == $meUser->created_at) {
                    $facebook = new Facebook();
                    // TODO add text
                    $facebook->sendMeMessage('Add text', $meUser->psid);
                }
            }

            return response();
        } else {
            // Step validation
            $hubChallenge = $request->get('hub_challenge');

            return response($hubChallenge, 200, ['Content-Type' => 'text/plain']);
        }
    }
}
