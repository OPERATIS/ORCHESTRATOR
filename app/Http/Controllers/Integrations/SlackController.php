<?php

namespace App\Http\Controllers\Integrations;

use App\Models\SlUser;
use App\Models\User;
use App\Services\Slack;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;

class SlackController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @return RedirectResponse
     */
    public function login(): RedirectResponse
    {
        $params = http_build_query([
            'client_id' => config('services.slack.client_id'),
            'redirect_uri' => config('services.slack.redirect'),
            'scope' => ['chat:write'],
        ]);

        return redirect()->to('https://slack.com/oauth/v2/authorize?' . $params);
    }

    public function callback(Request $request)
    {
        $code = $request->get('code');
        if ($code) {
            $slack = new Slack();
            $response = $slack->authV2Access($code);

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
                    $slack = new Slack();
                    // TODO add text
                    try {
                        $slack->chatPostMessage($slUser->access_token, $slUser->authed_user_id, 'add text');
                        // TODO add text
                        Session::flash('success-message', 'Connect slack added/updated');
                    } catch (GuzzleException $e) {
                        Session::flash('error-message', 'Some error please contact us');
                    }
                }
            } else {
                // TODO add text
                Session::flash('error-message', 'Some missing');
            }
        } else {
            // TODO add text
            Session::flash('error-message', 'Some missing');
        }

        return redirect('alerts');
    }
}
