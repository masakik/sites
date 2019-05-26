@extends('laravel-usp-theme::master')

@section('styles')
    @parent
    <link rel="stylesheet" href="/css/sites.css">
@stop

@section('javascripts_head')
    @parent
    <script src="/js/sites.js"></script>
@stop

@section('content')
    @include('messages.flash')
    @include('messages.errors')
@stop

@section('footer')
FFLCH sites
@stop
