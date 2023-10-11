<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AlertsController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $alerts = Alert::query()
            ->forNotifications()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(24)
            ->get();

        // Load connected
        $user->load(['telegrams', 'whatsApps', 'messengers', 'slacks']);

        return view('alerts.index')
            ->with('user', $user)
            ->with('connectedTelegram', count($user->telegrams) > 0)
            ->with('connectedWhatsApp', count($user->whatsApps) > 0)
            ->with('connectedMessenger', count($user->messengers) > 0)
            ->with('connectedSlack', count($user->slacks) > 0)
            ->with('alerts', $alerts);
    }
}
