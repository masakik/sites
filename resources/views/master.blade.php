@extends('adminlte::page')

@section('css')
    <link rel="stylesheet" href="/css/sites.css">
@stop

@section('js')
    <script src="/js/sites.js"></script>
@stop

@section('content')
    @include('messages.flash')
    @include('messages.errors')
@stop
