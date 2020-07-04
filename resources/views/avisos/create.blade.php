@extends('laravel-usp-theme::master')

@section('content')



<form method="POST" action="/avisos"> 
@csrf
<div class="card">
<div class="card-header">Cadastro de Avisos</div>
<div class="row">
  <div class="col-sm-8 form-group">
      <div class="card-body">
        <label class="card-title required" for="titulo">Título do Aviso: </label>
        <input type="text" class="form-control" id="titulo" name="titulo" value="">
      </div>
  </div>
  <div class="col-sm-4 form-group"> 
    <div class="card-body">
      <label class="card-title required" for="divulgacao_home_ate">Divulgação Até: </label>
      <input type="text" class="form-control datepicker" id="divulgacao_home_ate" name="divulgacao_home_ate" value="">
    </div>
  </div>
</div>  
<div class="row">
  <div class="col-sm form-group">
    <div class="card-body">
      <label class="card-title required" for="corpo">Corpo da mensagem: </label>
      <textarea class="form-control" id="corpo" name="corpo" rows="3"></textarea>
    </div>
  </div>
</div>
</br>
<div class="col-sm form-group">
  <button type="submit" class="btn btn-success">Salvar</button>
  <a class="btn btn-success" href="/avisos" role="button">Voltar</a>
</div>
   
</div>
</form>

@endsection('content')