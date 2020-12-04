<?php

namespace App\Http\Controllers;

use App\Models\Chamado;
use App\Models\Site;
use Illuminate\Http\Request;
use App\Mail\ChamadoMail;
use Mail;

class ChamadoController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function admin(Request $request)
    {
        $this->authorize('admin');

        if($request->search != null && $request->status != null){
            $chamados= Chamado::where('descricao', 'LIKE', "%{$request->search}%")
                ->where('status', $request->status)->paginate(10);
        }
        else if(isset($request->search)){
            $chamados= Chamado::where('descricao', 'LIKE', "%{$request->search}%")->paginate(10);
        }
        else if(isset($request->status) && ($request->status=='aberto' || $request->status=='fechado')){
            $chamados= Chamado::where('status', $request->status)->paginate(10);
        }
        else {
            $chamados= Chamado::paginate(10);
        }    
        
        return view('chamados/admin',compact('chamados'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Site $site)
    {
        $this->authorize('sites.view',$site);

        return view('chamados/index',compact('site'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Site $site)
    {
        $this->authorize('sites.view',$site);
        return view('chamados/create',compact('site'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Site $site)
    {
        $this->authorize('sites.view',$site);

        $request->validate([
          'descricao'  => ['required'],
          'tipo'       => ['required'],
        ]);
        $user = \Auth::user();
        $chamado = new Chamado;
        $chamado->descricao = $request->descricao;
        $chamado->tipo = $request->tipo;
        $chamado->status = 'aberto';
        $chamado->site_id = $site->id;
        $chamado->user_id = $user->id;
        $chamado->save();

        Mail::send(new ChamadoMail($chamado,$user));

        $request->session()->flash('alert-info', 'Chamado cadastrado com sucesso');
        return redirect("/sites/$site->id");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Chamado  $chamado
     * @return \Illuminate\Http\Response
     */
    public function show(Site $site, Chamado $chamado)
    {
        $this->authorize('sites.view',$site);
        return view('chamados/show',compact('site','chamado'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Chamado  $chamado
     * @return \Illuminate\Http\Response
     */
    public function edit(Chamado $chamado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Chamado  $chamado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chamado $chamado)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Chamado  $chamado
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chamado $chamado)
    {
        //
    }
}
