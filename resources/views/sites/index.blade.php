@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
@parent

    <form method="get" action="/sites">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Domínio ..." name="dominio">
            <span class="input-group-btn">
                <button type="submit" class="btn btn-success"> Buscar </button>
            </span>
        </div><!-- /input-group -->
    </form>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Domínio</th>
                <th>Responsável</th>
                <th>Números USP permitidos</th>
              {{--  <th>Status</th> --}}
                <th colspan="2" width="15%">Ações</th>
            </tr>
        </thead>
        <tbody>
@foreach ($sites as $site)
            <tr>
<td><a href="http://{{ $site->dominio }}{{ $dnszone }}" target="_blank">{{ $site->dominio }}{{ $dnszone }}</a></td>
<td>{{ $site->owner }}</td>
<td>
  <ul>
    @foreach(explode(',', $site->numeros_usp) as $numero_usp)
      <li>{{ $numero_usp }}</li>
    @endforeach
    @can('sites.update',$site)
       <a href="/sites/{{ $site->id }}/edit" class="btn btn-info">Alterar Números USP</a>
    @endcan
  </ul>
</td>
{{-- <td>{{ $site->status }}</td> --}}
<td>
    <ul  class="list-group">
        @can('sites.update',$site)
        <li class="list-group-item"><a href="/sites/{{ $site->id }}/changeowner" class="">Mudar Responsável</a></li>
        @endcan

        <li class="list-group-item"><a href="http://{{ $site->dominio }}{{ $dnszone }}/loginbytoken/?temp_token={{$hashlogin}}&codpes={{ Auth::user()->codpes }}" class="btn btn-info" target="_blank">Logon</a></li>

        @can('admin')
        <li class="list-group-item">
            <form method="POST" action="/sites/{{ $site->id }}">
            {{csrf_field()}} {{ method_field('delete') }}
            <button type="submit" class="delete-item btn btn-danger">Deletar</button>
        </li>
        @endcan
</form>

    </ul>
</td>

{{--
@if ($site->status == "Habilitado")
<td>
<form method="POST" action="/sites/{{ $site->id }}/disable">
{{ csrf_field() }}
<button type="submit" class="btn btn-info">Desabilitar</button>
</form></td>

@elseif ($site->status == "Desabilitado")
<td><form method="POST" action="/sites/{{ $site->id }}/enable">
{{ csrf_field() }}
<button type="submit" class="btn btn-success">Habilitar</button>
</form>

<form method="POST" action="/sites/{{ $site->id }}/delete">
{{ csrf_field() }}
<button type="submit" class="btn btn-dark">Deletar</button>
</form></td>

@elseif($site->status != "Servidor Offline")
<td>
<form method="POST" action="/sites/{{ $site->id }}/clone">
{{ csrf_field() }}
<button type="submit" class="btn btn-primary">Recriar</button>
</form>

</td>
@endif
--}}

</tr>
</tbody>

@endforeach

@stop
