@extends('adminlte::page')

@section('title', 'SolicitaSite')

@section('content_header')
    <h1>Sistema Solicitações de Sites</h1>
@stop

@section('content')
    
    
        @auth
            <h3><b>Olá {{ Auth::user()->name }},</b></h3>
            Acesse as opções no menu ao lado
        @else
            Você ainda não fez seu login com a senha única USP <a href="/senhaunica/login"> Faça seu Login! </a>
        @endauth
        <h3>Com o SolicitaSite você pode</h3>
        <ul>
            <li>Solictar sites </li>
            <li>Administrar seus sites solicitados</li>
            <li>...</li>
        </ul>
    
@stop
