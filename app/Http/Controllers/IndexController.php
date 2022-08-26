<?php

namespace App\Http\Controllers;

use App\Models\Site;

class IndexController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect('/sites');
        } else {
            $sites = Site::orderBy('dominio', 'ASC')->orderBy('categoria', 'ASC')->get();
            return view('index')->with('sites', $sites);
        }

    }
}
