@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')

@foreach ($sites as $site)

    <b>{{ $site->dominio }}{{ $dnszone }}</b> <a href="/sites/{{ $site->id }}/edit">Editar</a>

<form method="POST" action="/sites/{{ $site->id }}">
{{ csrf_field() }}
{{ method_field('delete') }}
<button type="submit">Apagar</button>
</form>

<br>

@endforeach

Sites aegir:
<br>
@foreach ($sites_aegir as $site)
    {{$site->title}}
    <br>status:{{$site->status}} <br><br>
@endforeach

@stop
