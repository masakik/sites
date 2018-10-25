@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
@parent
        <form method="POST" action="/sites">
        @csrf
        Domínio: <input name="dominio" class="form-control" placeholder="NÃO colocar o sufixo fflch.usp.br.">{{ $dnszone }}<br>
        Números USP: <input name="numeros_usp" class="form-control" placeholder="Relação de números USP, separados por vírgula, que terão permissão para administrar o site."><br>
        <button type="submit" class="btn btn-primary">Salvar</button>
        </form>
@stop
