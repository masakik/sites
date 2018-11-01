@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
@parent

<p>
    <a href="{{ route('sites.create') }}" class="btn btn-success">
        Solicitar um Site
    </a>
</p>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>URL</th>
                <th>Dono</th>
                <th>Números USP</th>
                <th>Status</th>
                <th colspan="2" width="15%">Ações</th>
            </tr>
        </thead>
        <tbody>
@foreach ($sites as $site)
            <tr>
<td><a href="http://{{ $site->dominio }}{{ $dnszone }}" target="_blank">{{ $site->dominio }}{{ $dnszone }}</a></td>
<td>{{ $site->owner }}</td>
<td>{{ $site->numeros_usp }}</td>
<td>{{ $site->status }}</td>
<td><a href="/sites/{{ $site->id }}/edit" class="btn btn-warning">Editar</a></td>
@if ($site->status == "Habilitado")
<td>
<form method="POST" action="/sites/{{ $site->id }}/disable">
{{ csrf_field() }}
<button type="submit" class="btn btn-info">Desabilitar Site</button>
</form></td>

@elseif ($site->status == "Desabilitado")
<td><form method="POST" action="/sites/{{ $site->id }}/enable">
{{ csrf_field() }}
<button type="submit" class="btn btn-success">Habilitar Site</button>
</form>

<form method="POST" action="/sites/{{ $site->id }}/delete">
{{ csrf_field() }}
<button type="submit" class="btn btn-dark">Deletar Site</button>
</form></td>

@else
<td>
<form method="POST" action="/sites/{{ $site->id }}/clone">
{{ csrf_field() }}
<button type="submit" class="btn btn-primary">Recriar Site</button>
</form></td>
@endif

</tr>
</tbody>
<br>

@endforeach

@stop
