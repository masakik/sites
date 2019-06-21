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
use Uspdev\Replicado\Pessoa;

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
        $sites = Site::allowed();

        // 1. query com a busca
        if(isset($request->dominio)) {
            $sites->where('dominio', 'LIKE', '%'.$request->dominio.'%');
        }

        // Dica de ouro para debugar SQL gerado:
        //dd($sites->toSql());

        // Executa a query
        $sites = $sites->orderBy('dominio')->paginate(3);

        // Busca o status dos sites no aegir
        /*
        foreach($sites as $site){
            $site->status = $this->aegir->verificaStatus($site->dominio.$dnszone);
        }
        */
        $this->novoToken();
        $hashlogin = $user = \Auth::user()->temp_token;
        return view('sites/index', compact('sites','dnszone','hashlogin'));
    }
    
    private function novoToken(){
        // gera um token de login no drupal
        $hashlogin = Str::random(32);
        $user = \Auth::user();
        $user->temp_token = $hashlogin;
        $user->save();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('sites.create');
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
        $this->authorize('sites.create');

        $request->validate([
          'dominio'     => ['required', 'alpha_num','unique:sites'],
          'categoria'   => ['required'],
        ]);
        
        $site = new Site;
        $dnszone = config('sites.dnszone');
        $site->dominio = $request->dominio;
        $site->categoria = $request->categoria;
        $site->status = 'solicitado';
        
        $site->owner = \Auth::user()->codpes;
        $site->save();

        //$alvo = $site->dominio . $dnszone;
        //clonaSiteAegir::dispatch($alvo);

        $request->session()->flash('alert-info', 'Solictação em andamento');
        return redirect("/sites/$site->id");
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
        $this->novoToken();
        $hashlogin = $user = \Auth::user()->temp_token;
        return view ('sites/show', compact('site','hashlogin'));
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
              'owner' => ['required',new Numeros_USP($request->owner)],
            ]);

            $site->owner = $request->owner;
            $request->session()->flash('alert-info','Responsável alterado com sucesso');
        }

        if (isset($request->categoria)) {
            $site->categoria = $request->categoria;
            $request->session()->flash('alert-info','categoria alterada com sucesso');
        }

        if (isset($request->novoadmin)) {
            $request->validate([
              'novoadmin' => ['required',new Numeros_USP($request->numeros_usp)],
            ]);

            $numeros_usp = explode(',',$site->numeros_usp);
            if(!in_array($request->novoadmin, $numeros_usp)){
                array_push($numeros_usp,$request->novoadmin);
            }
            $numeros_usp = array_map('trim', $numeros_usp);
            $numeros_usp = implode(',',$numeros_usp);
            $site->numeros_usp = $numeros_usp;
            $request->session()->flash('alert-info','Administrador adicionado com sucesso');
        }

        if (isset($request->deleteadmin)) {

            $numeros_usp = explode(',',$site->numeros_usp);
            if(in_array($request->deleteadmin, $numeros_usp)){
                $key = array_search($request->deleteadmin, $numeros_usp);
                unset($numeros_usp[$key]);
            }
            $numeros_usp = array_map('trim', $numeros_usp);
            $numeros_usp = implode(',',$numeros_usp);
            $site->numeros_usp = $numeros_usp;
            $request->session()->flash('alert-info','Administrador removido com sucesso');
        }

        if (isset($request->aprovar)) {
            $this->authorize('admin');
            $site->status = 'aprovado';
            $request->session()->flash('alert-info','Site autorizado com sucesso');
        }

        $site->save();
        return redirect("/sites/$site->id");
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

    public function novoAdmin(Site $site)
    {
        $this->authorize('sites.update',$site);
        return view('sites/novoadmin', compact('site'));
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

        // verifica se o temp_token está válido
        if($request->temp_token != $user->temp_token) {
            return response()->json([false,'Temp Token Inválido']);
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
