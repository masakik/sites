<?php

namespace App\Http\Controllers;

use App\Site;
use Illuminate\Http\Request;
use Auth;
use App\Jobs\criaSiteAegir;
use App\Jobs\desabilitaSiteAegir;
use App\Jobs\habilitaSiteAegir;
use App\Jobs\deletaSiteAegir;
use App\Jobs\clonaSiteAegir;
use App\Aegir\Aegir;

class SiteController extends Controller
{
    private $aegir;

    public function __construct()
    {
        $this->middleware('can:admin');
        $this->aegir = new Aegir;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dnszone = env('DNSZONE');
        $sites = Site::all()->where('owner',\Auth::user()->codpes)->sortBy('dominio');
        //$sites = Site::all();

        // Busca o status dos sites no aegir
        foreach($sites as $site){
            $site->status = $this->aegir->verificaStatus($site->dominio.$dnszone);
        }

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
      $dnszone = env('DNSZONE');
      $site->dominio = $request->dominio;
      $alvo = $site->dominio . $dnszone;
      $site->numeros_usp = $request->numeros_usp;
      $site->owner = \Auth::user()->codpes;
      $site->save();

      clonaSiteAegir::dispatch($alvo);

      $request->session()->flash('alert-info', 'Criação do site em andamento');
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
        $site->numeros_usp = $request->numeros_usp;
        if (isset($request->owner))
          $site->owner = $request->owner;
        else
          $site->owner = \Auth::user()->codpes;
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

    public function Owners(Request $request, $site)
    {
        /*
        if($request->apikey != env('AEGIR_KEY'))
        {
            return response('Unauthorized action.', 403);
        }
        */

        $dominio = str_replace('.fflch.usp.br','',$site);
        $site = Site::where('dominio',$dominio)->first();
        $numeros_usp = $site->owner . ','. str_replace(' ', '', $site->numeros_usp);

        return response()->json($numeros_usp);
    }

    public function cloneSite(Request $request, Site $site)
    {
      $dnszone = env('DNSZONE');
      $alvo = $site->dominio . $dnszone;
      clonaSiteAegir::dispatch($alvo);

      $request->session()->flash('alert-info', 'Clonagem do site em andamento');
      return redirect('/sites');
    }

    public function disableSite(Request $request, Site $site)
    {
      $dnszone = env('DNSZONE');
      $alvo = $site->dominio . $dnszone;
      desabilitaSiteAegir::dispatch($alvo);

      $request->session()->flash('alert-info', 'Desabilitação do site em andamento');
      return redirect('/sites');
    }

    public function enableSite(Request $request, Site $site)
    {
      $dnszone = env('DNSZONE');
      $alvo = $site->dominio . $dnszone;
      habilitaSiteAegir::dispatch($alvo);

      $request->session()->flash('alert-info', 'Habilitação do site em andamento');
      return redirect('/sites');
    }

    public function deleteSite(Request $request, Site $site)
    {
      $dnszone = env('DNSZONE');
      $alvo = $site->dominio . $dnszone;
      $site->delete();

      deletaSiteAegir::dispatch($alvo);

      $request->session()->flash('alert-info', 'Deleção do site em andamento');
      return redirect('/sites');
    }

    /**
     * Show the form for editing the owner.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function changeowner(Site $site)
    {
        return view('sites/changeowner', compact('site'));
    }
}
