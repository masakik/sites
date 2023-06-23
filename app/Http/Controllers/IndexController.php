<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redirect;

class IndexController extends Controller
{
    public function index()
    {
        if (session('loginRedirect')) {
            return Redirect::route('sites.index');
        }
        
        $sites = Site::orderBy('dominio', 'ASC')->orderBy('categoria', 'ASC')->get();
        return view('index')->with('sites', $sites);

    }
}
