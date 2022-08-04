@extends('layouts.app')

@section('content')
  @parent
  <form method="POST" action="avisos">
    @csrf
    <div class="card">
      <div class="card-header">Cadastro de Avisos</div>
      @include('avisos.form')
    </div>
  </form>
@endsection
