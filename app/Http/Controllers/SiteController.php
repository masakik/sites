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
use Illuminate\Support\Facades\Gate;
use App\Rules\Numeros_USP;
use App\Rules\Domain;

class SiteController extends Controller
{
    private $aegir;

    public function __construct()
    {
        $this->aegir = new Aegir;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //$this->middleware('can:index');
        $dnszone = env('DNSZONE');
        $filters = [];

        // 1. query com a busca
        if(isset($request->dominio)) {
            array_push($filters,['dominio','LIKE', '%'.$request->dominio.'%']);
        }

        // 2. Se usuário comum, mostrar só os que ele é dono.
        // Admistrador pode mostrar todos
        if(!Gate::allows('admin')) {
            array_push($filters,['owner','=', \Auth::user()->codpes]); 
        }

        // Executa a query
        $sites = Site::where($filters);
        $sites = $sites->get();

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
        $this->authorize('sites.create');
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
      $request->validate([
          'dominio'     => ['required', new Domain],
          'numeros_usp' => [new Numeros_USP($request->numeros_usp)],
      ]);

        $this->authorize('sites.create');
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
        $this->authorize('sites.view',$site);
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
        $this->authorize('sites.update',$site);
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
      $request->validate([
          'dominio'     => 'required',
          'numeros_usp' => [new Numeros_USP($request->numeros_usp)],
          'owner' => 'numeric'
      ]);

        $this->authorize('sites.update',$site);
        $site->dominio = $request->dominio;
        $site->numeros_usp = $request->numeros_usp;
        if (isset($request->owner)) {
            $site->owner = $request->owner;
            $request->session()->flash('alert-info','Responsável alterado com sucesso');
        }
        else {
            $site->owner = \Auth::user()->codpes;
            $request->session()->flash('alert-info','Atualização efetuada com sucesso');
        }
        $site->save();
        return redirect("/sites");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Site $site)
    {
        $this->authorize('sites.delete',$site);
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
      $this->authorize('sites.update',$site);
      $dnszone = env('DNSZONE');
      $alvo = $site->dominio . $dnszone;
      clonaSiteAegir::dispatch($alvo);

      $request->session()->flash('alert-info', 'Clonagem do site em andamento');
      return redirect('/sites');
    }

    public function disableSite(Request $request, Site $site)
    {
      $this->authorize('sites.update',$site);
      $dnszone = env('DNSZONE');
      $alvo = $site->dominio . $dnszone;
      desabilitaSiteAegir::dispatch($alvo);

      $request->session()->flash('alert-info', 'Desabilitação do site em andamento');
      return redirect('/sites');
    }

    public function enableSite(Request $request, Site $site)
    {
      $this->authorize('sites.update',$site);
      $dnszone = env('DNSZONE');
      $alvo = $site->dominio . $dnszone;
      habilitaSiteAegir::dispatch($alvo);

      $request->session()->flash('alert-info', 'Habilitação do site em andamento');
      return redirect('/sites');
    }

    public function deleteSite(Request $request, Site $site)
    {
      $this->authorize('sites.delete',$site);
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
        $this->authorize('sites.update',$site);
        return view('sites/changeowner', compact('site'));
    }
}
