@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
@parent

<form method="POST" action="/sites/{{ $site->id }}">
{{ csrf_field() }}
{{ method_field('patch') }}
 
Domínio: <input name="dominio" class="form-control" value="{{ $site->dominio }}">
Números USP: <input name="numeros_usp" class="form-control" value="{{ $site->numeros_usp }}">
<button type="submit" class="btn btn-primary"> Salvar </button>
</form>

@stop
