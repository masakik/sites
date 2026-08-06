<form method="get" action="sites">
  <div class="input-group">
    <div class="input-group-prepend">
      <span class="input-group-text">Buscar</span>
    </div>
    <input type="text" class="form-control" placeholder="Domínio ..." name="dominio" value="{{ $filters['dominio'] ?? '' }}">

    <select class="custom-select" id="status" name="status">
      <option value="" @selected(empty($filters['status']))>Todos os status</option>
      @foreach ($statuses as $status)
        <option value="{{ $status }}" @selected(($filters['status'] ?? null) === $status)>
          {{ $status }}
        </option>
      @endforeach
    </select>

    <select class="custom-select" id="categoria" name="categoria">
      <option value="">Todas as categorias</option>
      @foreach ($categories as $categoria)
        <option value="{{ $categoria }}" @selected(($filters['categoria'] ?? null) === $categoria)>{{ $categoria }}</option>
      @endforeach
    </select>

    <div class="input-group-append">
      <button type="submit" class="btn btn-success input-group-btn">OK</button>
    </div>

  </div><!-- /input-group -->
</form>
