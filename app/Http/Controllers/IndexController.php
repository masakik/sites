<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Site;

class indexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    public function index(){
        return view('index')->with('sites',Site::get());
    }
}
