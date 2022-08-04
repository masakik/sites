@extends('layouts.app')

@section('content')
  @parent

  <form method="POST" role="form" action="sites">
    @csrf
    Domínio:
    <div class="form-group form-inline">
      <label for="dominio"> </label>
      <input name="dominio" class="form-control dominio" placeholder="meuqueridosite" id="dominio"
        value="{{ old('dominio') }}" onkeyup="this.value = this.value.toLowerCase();">
      <b> {{ $dnszone }} </b>
    </div>

    <div class="form-group">
      <label for="justificativa">Justificativa:</label>
      <textarea class="form-control" id="justificativa" rows="5" name="justificativa">{{ old('justificativa') }}</textarea>
    </div>

    <div class="form-group">
      <label for="categoria">Categoria:</label>
      <select class="form-control" id="categoria" name="categoria">
        <option>Escolha uma categoria ..</option>
        @foreach (App\Models\Site::categorias() as $categoria)
          <option {{ old('categoria') == $categoria ? 'selected' : '' }}>{{ $categoria }}</option>
        @endforeach
      </select>
    </div>

    <div class="form-group">
      <button type="submit" class="btn btn-primary">Enviar</button>
    </div>
  </form>

@stop

@section('javascripts_bottom')
  @parent
  <script>
    CKEDITOR.replace('justificativa');
  </script>
@stop
