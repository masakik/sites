@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
  @parent
  <form method="POST" role="form" action="{{ route('chamados.store', [$site->id]) }}">
    @csrf

  <div class="form-group">
    <label for="tipo">Categoria</label>
    <select class="form-control" id="tipo" name="tipo">
      <option>Problema</option>
      <option>Solicitação de módulo</option>
      <option>Dúvida</option>
      <option>Sugestão</option>
      <option>Reclamação</option>
    </select>
  </div>

  <div class="form-group">
    <label for="descricao">Descrição</label>
    <textarea class="form-control" id="descricao" name="descricao" rows="7"></textarea>
  </div>


        <div class="form-group">
        <button type="submit" class="btn btn-primary">Enviar</button>
        </div>
    </form>

@stop
