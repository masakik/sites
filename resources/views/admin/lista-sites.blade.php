@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')

@foreach ($sites as $site)

@foreach ($sites_aegir as $site_aegir)
@if ($site->dominio.$dnszone == $site_aegir->title)
    <?php $status = $site_aegir->status; ?>
@endif
@endforeach

    <b>{{ $site->dominio }}{{ $dnszone }} {{ $status or "site não existe" }}</b> <a href="/sites/{{ $site->id }}/edit">Editar</a>

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
