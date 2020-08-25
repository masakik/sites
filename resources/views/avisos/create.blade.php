@extends('laravel-usp-theme::master')

@section('styles')
  <link rel="stylesheet" type="text/css" href="{{asset('/css/sites.css')}}">
@endsection('styles')

@section('javascripts_head')
  <script src="{{asset('/js/sites.js')}}"></script>
@endsection('javascript_head')

@section('content')
@include('messages.flash')
@include('messages.errors')


<form method="POST" action="/avisos"> 
@csrf
<div class="card">
<div class="card-header">Cadastro de Avisos</div>
  @include('avisos.form')
</div>
</form>

@endsection('content')