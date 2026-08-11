@extends('layouts.app')

@section('content')
  <form method="POST" action="{{ route('avisos.update', $aviso) }}">
    @csrf
    @method('patch')
    <div class="card">
      <div class="card-header">Edição de Aviso</div>
      @include('avisos.form')
    </div>
  </form>
@endsection
