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
<td>{{ $site->status or 'Em processamento' }}</td>
<td><a href="/sites/{{ $site->id }}/edit" class="btn btn-warning">Editar</a></td>
<td>
<form method="POST" action="/sites/{{ $site->id }}">
{{ csrf_field() }}
{{ method_field('delete') }}
<button type="submit" class="delete-item btn btn-danger">Apagar</button>
</form>
</td>
</tr>
</tbody>
<br>

@endforeach

@stop
