@extends('layouts.app')

@section('content')
  @parent
  <form method="POST" role="form" class="form-inline" action="sites/{{ $site->id }}">
    {{ csrf_field() }}
    {{ method_field('patch') }}

    <div class="form-group">
      <label>Número USP do(a) novo(a) administrador:</label>
      <input name="novoadmin" class="form-control" value="">
    </div>
    <button type="submit" class="btn btn-primary"> Enviar </button>
  </form>

@stop
