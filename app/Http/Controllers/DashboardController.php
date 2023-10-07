<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Metrics;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $metricsActualData = Metrics::getActualData($user->id);

        return view('dashboard.index')
            ->with('user', $user)
            ->with('metricsActualData', $metricsActualData);
    }
}
