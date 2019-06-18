@extends('master')

@section('title', 'SolicitaSite')

@section('content_header')
    <h1>Sites</h1>
@stop

@section('content')
    @parent
        @auth
            <h3><b>Olá {{ Auth::user()->name }},</b></h3>
            Acesse as opções no menu ao lado
        @else
            Você ainda não fez seu login com a senha única USP <a href="/senhaunica/login"> Faça seu Login! </a>
        @endauth
@stop
