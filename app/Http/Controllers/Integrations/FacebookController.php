<?php

namespace App\Http\Controllers\Integrations;

use App\Models\Integration;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @return mixed
     */
    public function login()
    {
        return Socialite::driver('facebook')
            ->setScopes(['ads_read'])
            ->redirect();
    }

    public function callback()
    {
        /** @var User $user */
        $user = Auth::user();

        try {
            $socialiteUser = Socialite::driver('facebook')
                ->user();
        } catch (\Exception $exception) {
            // TODO add text
            Session::flash('success-message', 'Some error please contact us');
            return redirect('integrations');
        }

        Integration::updateOrCreate([
            'user_id' => $user->id,
            'app_user_slug' => $socialiteUser->getNickname() ?? null,
            'app_user_id' => $socialiteUser->getId(),
            'platform' => 'facebook'
        ], [
            'access_token' => $socialiteUser->token,
            'expires_in' => $socialiteUser->expiresIn,
            'scope' => implode(',', $socialiteUser->approvedScopes)
        ]);

        // TODO add text
        Session::flash('success-message', 'Connect shopify added/updated');
        return redirect('integrations');
    }
}
