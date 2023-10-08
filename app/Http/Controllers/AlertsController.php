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
//            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(24)
            ->get();


        return view('alerts.index')
            ->with('user', $user)
            ->with('alerts', $alerts);
    }
}
