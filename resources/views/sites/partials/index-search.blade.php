<form method="get" action="sites">
  <div class="input-group">
    <div class="input-group-prepend">
      <span class="input-group-text">Buscar</span>
    </div>
    <input type="text" class="form-control" placeholder="Domínio ..." name="dominio" value="{{ Request()->dominio }}">

    <select class="custom-select" id="status" name="status">
      <option value="" selected>Todos os status</option>
      @foreach (App\Models\Site::status() as $status)
        <option value="{{ $status }}" @if (Request()->status == "$status") selected @endif>
          {{ $status }}
        </option>
      @endforeach
    </select>

    <select class="custom-select" id="categoria" name="categoria">
      <option value="">Todas as categorias</option>
      @foreach (App\Models\Site::categorias() as $categoria)
        <option {{ Request()->categoria == $categoria ? 'selected' : '' }}>{{ $categoria }}</option>
      @endforeach
    </select>

    <div class="input-group-append">
      <button type="submit" class="btn btn-success input-group-btn">OK</button>
    </div>

  </div><!-- /input-group -->
</form>
