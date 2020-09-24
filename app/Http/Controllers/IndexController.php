<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Site;

class IndexController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index']);
    }

    public function index(){
        return view('index')->with('sites',Site::orderBy('dominio', 'ASC')->get());
    }
}
