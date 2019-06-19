@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
@parent

<form method="POST" action="/sites/{{ $site->id }}">
{{ csrf_field() }}
{{ method_field('patch') }}
 
  <div class="form-group">
    <label for="categoria">Categoria</label>
    <select class="form-control" id="categoria" name="categoria">
      <option selected>{{$site->categoria}}</option>
      <option>Grupo de estudo</option>
      <option>Grupo de pesquisa</option>
      <option>Departamento</option>
      <option>Administrativo</option>
      <option>Centro</option>
      <option>Associação</option>
      <option>Laboratório</option>
      <option>Comissão</option>
      <option>Outro</option>
    </select>
  </div>
<br>
<button type="submit" class="btn btn-primary"> Enviar </button>
</form>

@stop
