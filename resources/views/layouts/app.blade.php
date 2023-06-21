@extends('laravel-usp-theme::master')

@section('styles')
    @parent
    <link rel="stylesheet" href="css/sites.css">
@stop

@include('laravel-usp-theme::blocos.sticky')

@section('javascripts_bottom')
    @parent
    <script src="js/sites.js"></script>
    <script src="https://cdn.ckeditor.com/4.19.1/standard/ckeditor.js"></script>
@stop

@section('content')
    @include('messages.flash')
    @include('messages.errors')
@stop