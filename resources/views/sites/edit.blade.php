@extends('layouts.app')

@section('content')
  @parent

  <div class="h5">
    Editando <a href="sites/{{ $site->id }}">{{ $site->dominio }}{{ config('sites.dnszone') }}</a>
  </div>

  <form method="POST" action="sites/{{ $site->id }}">
    @csrf
    @method('patch')

    <x-input.text label="Domínio" name="dominio" value="{{ $site->dominio }}"></x-input-text>

    <div class="form-group">
      <label for="justificativa">Justificativa:</label>
      <textarea class="form-control" id="justificativa" rows="5" name="justificativa">{{ $site->justificativa }}</textarea>
    </div>

    <div class="form-group">
      <label for="categoria">Categoria</label>
      <select class="form-control" id="categoria" name="categoria">
        @foreach (App\Models\Site::categorias() as $categoria)
          <option {{ (old('categoria') == $categoria || $site->categoria == $categoria) ? 'selected' : '' }}>{{ $categoria }}</option>
        @endforeach
      </select>
    </div>
    <br>
    <button type="submit" class="btn btn-primary"> Enviar </button>
  </form>

@stop

@section('javascripts_bottom')
  @parent
  <script>
    CKEDITOR.replace('justificativa');
  </script>
@stop
