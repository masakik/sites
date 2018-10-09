<?php

namespace App\Http\Controllers;

use App\Site;
use Illuminate\Http\Request;
use Auth;
use GuzzleHttp\Client;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dnszone = env('DNSZONE');
        $sites = Site::all()->where('owner',\Auth::user()->id)->sortBy('dominio');
        return view('sites/index', compact('sites','dnszone'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dnszone = env('DNSZONE');
        return view('sites/create', ['dnszone'=>$dnszone]); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $site = new Site;
      $site->dominio = $request->dominio;
      $site->owner = \Auth::user()->id;
      $site->save();

      $dnszone = env('DNSZONE');
      $alvo = $site->dominio . $dnszone;
      $site_modelo = 'modelod8.fflch.usp.br';

      $client = new Client([
           'base_uri' => 'http://aegir.fflch.usp.br'
      ]);

      $res = $client->request('POST','/aegir/saas/task/', [
          'form_params' => [
              'target' => $site_modelo,
              'type' => 'clone',
              'options[new_uri]' => $alvo,
              'options[database]' => 4,
              'options[target_platform]' => 155,
              'options[client_email]' => 'fflch@usp.br',
              'options[client_name]' => 'fflch',
              'api-key' => 'ZYODpIU-GhDtTJThA2Z-HQ'
          ]
      ]);

      return redirect('/sites');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function show(Site $site)
    {
        return view ('sites/show', compact('site'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function edit(Site $site)
    {
        return view('sites/edit', compact('site'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Site $site)
    {
        $site->dominio = $request->dominio;
        $site->owner = \Auth::user()->id;
        $site->save();
        return redirect("/sites/$site->id");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Site $site)
    {
        $site->delete();
        return redirect('/');
    }
}
