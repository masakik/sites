<?php

namespace App\Http\Controllers;

use App\Comentario;
use App\Chamado;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ComentarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Chamado $chamado)
    {
        $request->validate([
          'comentario'  => ['required'],
        ]);

        $comentario = new Comentario;
        $comentario->comentario = $request->comentario;
        $comentario->chamado_id = $chamado->id;
        $comentario->user_id = \Auth::user()->id;
        $comentario->save();

        if(isset($request->status)) {
            if($request->status == 'fechar') {
                $comentario->chamado->status = 'fechado';
                $comentario->chamado->fechado_em = Carbon::now();
            }
            elseif($request->status == 'abrir') {
                $comentario->chamado->status = 'aberto';
                $comentario->chamado->fechado_em = null;
            }
            $comentario->chamado->save();
        }

        $request->session()->flash('alert-info', 'Comentário enviado com sucesso');
        return redirect("/chamados/$chamado->site_id/$chamado->id");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comentario  $comentario
     * @return \Illuminate\Http\Response
     */
    public function show(Comentario $comentario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comentario  $comentario
     * @return \Illuminate\Http\Response
     */
    public function edit(Comentario $comentario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comentario  $comentario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comentario $comentario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comentario  $comentario
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comentario $comentario)
    {
        //
    }
}
