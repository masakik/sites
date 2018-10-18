@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
@parent

<h1>{{ $site->dominio }}</h1>

@stop
