<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SlUser;
use App\Models\User;
use App\Services\Slack;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SlackController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $params = http_build_query([
            'client_id' => config('services.slack.client_id'),
            'redirect_uri' => config('services.slack.redirect'),
            'scope' => ['chat:write'],
        ]);

        return response()->json([
            'status' => true,
            'url' => 'https://slack.com/oauth/v2/authorize?' . $params
        ]);
    }

    public function callback(Request $request): JsonResponse
    {
        $validateUser = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validateUser->errors()
            ], 401);
        }

        $code = $request->get('code');

        $slack = new Slack();
        try {
            $response = $slack->authV2Access($code);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        } catch (GuzzleException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 401);
        }

        if ($response->ok) {
            /** @var User $user */
            $user = Auth::user();

            $slUser = SlUser::updateOrCreate([
                'user_id' => $user->id,
                'authed_user_id' => $response->authed_user->id
            ], [
                'access_token' => $response->access_token
            ]);

            if (Carbon::parse($slUser->created_at)->seconds(0)->toDateTimeString() === Carbon::now()->seconds(0)->toDateTimeString()) {
                try {
                    $slack->chatPostMessage($slUser->access_token, $slUser->authed_user_id, 'add text');
                } catch (\Exception $e) {
                    Log::error($e->getMessage());
                } catch (GuzzleException $e) {
                    Log::error($e->getMessage());
                }

                return response()->json([
                    'status' => true,
                ]);
            } else {
                return response()->json([
                    'status' => true,
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
            ], 500);
        }
    }
}
