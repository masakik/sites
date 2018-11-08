@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
@parent
<form method="POST" action="/sites/{{ $site->id }}">
{{ csrf_field() }}
{{ method_field('patch') }}
 
<input type="hidden" name="dominio" class="form-control" value="{{ $site->dominio }}">
<input type="hidden" name="numeros_usp" class="form-control" value="{{ $site->numeros_usp }}">
Dono: <input name="owner" class="form-control" value="{{ $site->owner }}">
<button type="submit" class="btn btn-primary"> Salvar </button>
</form>

@stop
