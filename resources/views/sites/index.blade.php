@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
@parent

@foreach ($sites as $site)

{{ $site->dominio }}{{ $dnszone }} <a href="/sites/{{ $site->id }}/edit">Editar</a>

<form method="POST" action="/sites/{{ $site->id }}">
{{ csrf_field() }}
{{ method_field('delete') }}
<button type="submit">Apagar</button>
</form>

<br>

@endforeach

@stop
