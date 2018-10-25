@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>URL</th>
                <th>URL2</th>
                <th>Dono</th>
                <th>Números USP</th>
                <th>Status</th>
                <th colspan="2" width="15%">Ações</th>
                <th width="10%">AEGIR</th>
            </tr>
        </thead>
        <tbody>

@foreach ($sites as $site)

@foreach ($sites_aegir as $site_aegir)
<?php
    if($site->dominio.$dnszone == $site_aegir->title){
      if ($site_aegir->status == 1){
        $site_status_aegir = "Habilitado";
        $site_aegir_title = $site_aegir->title;
      }
      else{
        $site_status_aegir = "Desabilitado";
        $site_aegir_title = $site_aegir->title;
      }
    }
    else{
      $site_status_aegir = "Não Existe";
      $site_aegir_title = $site_aegir->title;
    }
?>
@endforeach

<tr>
<td><a href="http://{{ $site->dominio }}{{ $dnszone }}" target="_blank">{{ $site->dominio }}{{ $dnszone }}</a></td>
<td><a href="http://{{ $site_aegir_title }}" target="_blank">{{ $site_aegir_title }}</a></td>
<td> {{ $site->owner }}</td>
<td> {{ $site->numeros_usp }}</td>
<td> {{ $site_status_aegir }}</td>
<td> <a href="/sites/{{ $site->id }}/edit" class="btn btn-warning">Editar</a></td>
<td>
<form method="POST" action="/sites/{{ $site->id }}">
{{ csrf_field() }}
{{ method_field('delete') }}
<button type="submit" class="delete-item btn btn-danger">Apagar</button>
</form></td>
@if ($site_status_aegir == "Habilitado")
<td>
<form method="POST" action="/admin/{{ $site->id }}/disable">
{{ csrf_field() }}
<button type="submit" class="btn btn-info">Desabilitar Site</button>
</form></td>

@elseif ($site_status_aegir == "Desabilitado")
<td><form method="POST" action="/admin/{{ $site->id }}/enable">
{{ csrf_field() }}
<button type="submit" class="btn btn-success">Habilitar Site</button>
</form>

<form method="POST" action="/admin/{{ $site->id }}/delete">
{{ csrf_field() }}
<button type="submit" class="btn btn-dark">Deletar Site</button>
</form></td>

@else
<td>
<form method="POST" action="/admin/{{ $site->id }}/clone">
{{ csrf_field() }}
<button type="submit" class="btn btn-primary">Criar Site</button>
</form></td>
@endif
</tr>
@endforeach
        </tbody>
    </table>
</div>
@stop
