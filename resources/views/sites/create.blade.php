@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
        <form method="POST" action="/sites">
        @csrf
        Domínio: <input name="dominio">{{ $dnszone }}<br>
        Números USP: <input name="numeros_usp"><br>
        <button type="submit">Salvar</button>
        </form>
@stop
