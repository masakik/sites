@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
@parent

<form method="POST" action="/sites/{{ $site->id }}">
{{ csrf_field() }}
{{ method_field('patch') }}
 
<h3>Números USP:</h3> 

<input name="numeros_usp" class="form-control" value="{{ $site->numeros_usp }}">
<br>
Relação de números USP, separados por vírgula, que terão permissão para administrar o site.

<br>
<br>
<button type="submit" class="btn btn-primary"> Enviar </button>
</form>

@stop
