<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Site;

class IndexController extends Controller
{
    public function index(){
        if(auth()->check()){
            return redirect('/sites');
        } else {
            return view('index')->with('sites',Site::orderBy('dominio', 'ASC')->get());
        }
        
    }
}
