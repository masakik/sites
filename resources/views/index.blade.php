@extends('master')

@section('content_header')
    <h1>Sites</h1>
@stop

@section('content')
    @parent
    @include('sites.partials.list')
@endsection('content')
