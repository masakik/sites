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
@foreach ($sites as $site)
<?php
    $res = $client->request('GET','/aegir/saas/site/'.$site->dominio . $dnszone. '.json', ['query' => ['api-key' => 'ZYODpIU-GhDtTJThA2Z-HQ']]);
    $site_aegir = json_decode($res->getBody());
    $existe_site = array_key_exists("site_status",$site_aegir);
    if($existe_site == false){
        $site_status = "não existe";
    }
    else{
        $site_status = $site_aegir->site_status;
        if($site_status == 1){
            $site_status = "habilitado";
        }
        else{
            $site_status = "desabilitado";
        }
    }
?>

{{ $site->dominio }}{{ $dnszone }} {{ $site->owner }} {{ $site_status }} <a href="/sites/{{ $site->id }}/edit">Editar</a>

<form method="POST" action="/sites/{{ $site->id }}">
{{ csrf_field() }}
{{ method_field('delete') }}
<button type="submit">Apagar</button>
</form>
<br>
@endforeach
@stop
