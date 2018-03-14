@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')

@foreach ($sites as $site)

{{ $site->dominio }}{{ $dnszone }} <br>

@endforeach

@stop
