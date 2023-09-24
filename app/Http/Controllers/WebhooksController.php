<?php

namespace App\Http\Controllers;

use App\Models\WaUser;
use Illuminate\Http\Request;

class WebhooksController extends Controller
{
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
                WaUser::updateOrCreate([
                    'user_id' => $userId,
                    'display_phone_number' => $displayPhoneNumber,
                    'phone_number_id' => $phoneNumberId,
                ], [
                    'username' => $username
                ]);
            }

            return response();
        } else {
            // Step validation
            $hubMode = $request->get('hub_mode');
            $hubChallenge = $request->get('hub_challenge');
            $hubVerifyToken = $request->get('hub_verify_token');

            return response($hubChallenge, 200, ['Content-Type' => 'text/plain']);
        }
    }
}
