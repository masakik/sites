@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
<?php
use GuzzleHttp\Client;

$client = new Client([
             'base_uri' => 'http://aegir.fflch.usp.br',
        ]);

?>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>URL</th>
                <th>Dono</th>
                <th>Números USP</th>
                <th>Status</th>
                <th colspan="2" width="15%">Ações</th>
                <th width="10%">AEGIR</th>
            </tr>
        </thead>
        <tbody>

@foreach ($sites as $site)
<?php
    $res = $client->request('GET','/aegir/saas/site/'.$site->dominio . $dnszone. '.json', ['query' => ['api-key' => 'ZYODpIU-GhDtTJThA2Z-HQ']]);
    $site_aegir = json_decode($res->getBody());
    $existe_site = array_key_exists("site_status",$site_aegir);
    if($existe_site == false){
        $site_status = "Não Existe";
    }
    else{
        $site_status = $site_aegir->site_status;
        if($site_status == 1){
            $site_status = "Habilitado";
        }
        else{
            $site_status = "Desabilitado";
        }
    }
?>

<tr>
<td><a href="http://{{ $site->dominio }}{{ $dnszone }}" target="_blank">{{ $site->dominio }}{{ $dnszone }}</a></td>
<td> {{ $site->owner }}</td>
<td> {{ $site->numeros_usp }}</td>
<td> {{ $site_status }}</td>
<td> <a href="/sites/{{ $site->id }}/edit" class="btn btn-warning">Editar</a></td>
<td>
<form method="POST" action="/sites/{{ $site->id }}">
{{ csrf_field() }}
{{ method_field('delete') }}
<button type="submit" class="delete-item btn btn-danger">Apagar</button>
</form></td>
@if ($site_status == "Habilitado")
<td>
<form method="POST" action="/admin/{{ $site->id }}/disable">
{{ csrf_field() }}
<button type="submit" class="btn btn-info">Desabilitar Site</button>
</form></td>

@elseif ($site_status == "Desabilitado")
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
