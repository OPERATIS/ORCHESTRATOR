<?php

namespace App\Http\Controllers;

use Artesaos\SEOTools\Facades\SEOTools;

class PagesController extends Controller
{
    public function index()
    {
        return redirect('dashboard');
    }

    public function thankYou()
    {
        return view('thank-you');
    }

    public function error404()
    {
        SEOTools::setTitle('404 Error | ORCHESTRATOR');
        return view('errors.404');
    }
    public function error500()
    {
        SEOTools::setTitle('500 Error | ORCHESTRATOR');
        return view('errors.500');
    }
}
