@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
@parent
<form method="POST" role="form" class="form-inline" action="/sites/{{ $site->id }}">
{{ csrf_field() }}
{{ method_field('patch') }}

        <div class="form-group">
            <label>Número USP do(a) novo(a) responsável pelo site:</label>

             <input name="owner" class="form-control" value="{{ $site->owner }}">
        </div>
            <button type="submit" class="btn btn-primary"> Enviar </button>
</form>

@stop
