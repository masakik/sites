<?php

namespace App\Http\Controllers;

use App\Site;
use App\User;
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
use Illuminate\Support\Str;

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
        $this->authorize('sites.create'); // verificar porque isso não funciona
        $dnszone = config('sites.dnszone');

        # todos sites
        $sites = Site::where([]);

        // 1. query com a busca
        if(isset($request->dominio)) {
            $sites->OrWhere('dominio', 'LIKE', '%'.$request->dominio.'%');
        }

        // 2. Se usuário comum, mostrar só os que ele é responsável.
        // Admistrador pode mostrar todos
        if(!Gate::allows('admin')) {
            $sites->OrWhere('owner', '=', \Auth::user()->codpes);
            $sites->OrWhere('numeros_usp', 'LIKE', '%'.\Auth::user()->codpes.'%'); 
        }

        // Executa a query
        $sites = $sites->get();

        // Busca o status dos sites no aegir
        /*
        foreach($sites as $site){
            $site->status = $this->aegir->verificaStatus($site->dominio.$dnszone);
        }
        */

        // gera um token de login no drupal
        $hashlogin = Str::random(32);
        $user = \Auth::user();
        $user->temp_token = $hashlogin;
        $user->save();

        return view('sites/index', compact('sites','dnszone','hashlogin'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        #$this->authorize('sites.create');
        $this->authorize('admin');
        $dnszone = config('sites.dnszone');
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
        #$this->authorize('sites.create');
        $this->authorize('admin');

        $request->validate([
          'dominio'     => ['required', 'alpha_num','unique:sites'],
          'numeros_usp' => [new Numeros_USP($request->numeros_usp)],
        ]);

        $site = new Site;
        $dnszone = config('sites.dnszone');
        $site->dominio = $request->dominio;
        $alvo = $site->dominio . $dnszone;
        $site->numeros_usp = $request->numeros_usp;
        $site->owner = \Auth::user()->codpes;
        $site->save();

        //clonaSiteAegir::dispatch($alvo);

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
        $this->authorize('sites.update',$site);

        if (isset($request->owner)) {
            $request->validate([
              'owner' => 'integer'
            ]);
            $site->owner = $request->owner;
            $request->session()->flash('alert-info','Responsável alterado com sucesso');
        }

        if (isset($request->numeros_usp)) {
            $request->validate([
              'numeros_usp' => [new Numeros_USP($request->numeros_usp)],
            ]);

            $site->numeros_usp = $request->numeros_usp;
            $request->session()->flash('alert-info','Números USP alterados com sucesso');
        }

        $site->save();
        return redirect("/sites");
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function destroy(Site $site)
    {
        $this->authorize('admin');
        #$this->authorize('sites.delete',$site);
        $site->delete();
        return redirect('/sites');
    }

    public function check(Request $request)
    {

        $request->validate([
          'temp_token' => ['required', 'alpha_num'],
          'codpes'     => ['required','integer'],
          'site'       => ['required'],
        ]);

        $user = User::where('codpes',$request->codpes)->first();

        // verifica se token secreto é válido
        if($request->secretkey != config('sites.deploy_secret_key'))
            return response()->json([false,'Secret Token Inválido']); 

        // verifica se o temp_token está válido e caso esteja, invalide-o,
        // pois ele só deve ser usado uma vez
        if($request->temp_token != $user->temp_token) {
            return response()->json([false,'Temp Token Inválido']);
        } else {
            $user->temp_token = '';
            $user->save();
        }

        // verifica se site existe
        $dominio = str_replace('.fflch.usp.br','',$request->site);
        $site = Site::where('dominio',$dominio)->first();
        if($site) {
            // verifica se o número usp em questão pode fazer logon no site
            $all = $site->owner . ',' . $site->numeros_usp . ',' . config('sites.admins');
            if(in_array($request->codpes,explode(",",$all))) {
                return response()->json([true,$user->email]); 
            }
            return response()->json([false,'Usuário sem permissão']);
        }
        return response()->json([false,'Site não existe']);
    }

/*
    public function cloneSite(Request $request, Site $site)
    {
      $this->authorize('sites.update',$site);
      $dnszone = config('sites.dnszone');
      $alvo = $site->dominio . $dnszone;
      clonaSiteAegir::dispatch($alvo);

      $request->session()->flash('alert-info', 'Clonagem do site em andamento');
      return redirect('/sites');
    }

    public function disableSite(Request $request, Site $site)
    {
      $this->authorize('admin');
      #$this->authorize('sites.update',$site);
      $dnszone = config('sites.dnszone');
      $alvo = $site->dominio . $dnszone;
      desabilitaSiteAegir::dispatch($alvo);

      $request->session()->flash('alert-info', 'Desabilitação do site em andamento');
      return redirect('/sites');
    }

    public function enableSite(Request $request, Site $site)
    {
      $this->authorize('admin');
      #$this->authorize('sites.update',$site);
      $dnszone = config('sites.dnszone');
      $alvo = $site->dominio . $dnszone;
      habilitaSiteAegir::dispatch($alvo);

      $request->session()->flash('alert-info', 'Habilitação do site em andamento');
      return redirect('/sites');
    }

    public function deleteSite(Request $request, Site $site)
    {
      $this->authorize('admin');
      #$this->authorize('sites.delete',$site);
      $dnszone = config('sites.dnszone');
      $alvo = $site->dominio . $dnszone;
      $site->delete();

      //deletaSiteAegir::dispatch($alvo);

      $request->session()->flash('alert-info', 'Deleção do site em andamento');
      return redirect('/sites');
    }
    */

}
