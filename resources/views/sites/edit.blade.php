@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
<form method="POST" action="/sites/{{ $site->id }}">
{{ csrf_field() }}
{{ method_field('patch') }}
 
Domínio: <input name="dominio" value="{{ $site->dominio }}">
Números USP: <input name="numeros_usp" value="{{ $site->numeros_usp }}">
<button type="submit"> Salvar </button>
</form>

@stop
