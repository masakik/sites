<div>
  <form method="get" action="sites">
    <div class="row">
      <div class="input-group">

        <input type="text" class="form-control" placeholder="Domínio ..." name="dominio" value="{{ Request()->dominio }}">

        <select class="custom-select" id="status" name="status">
          <option value="" selected>
            Todos
          </option>
          @foreach (App\Models\Site::status() as $status)
            <option value="{{ $status }}" @if (Request()->status == "$status") selected @endif>
              {{ $status }}
            </option>
          @endforeach
        </select>

        <span class="input-group-btn">
          <button type="submit" class="btn btn-success"> Buscar </button>
        </span>

      </div>
    </div><!-- /input-group -->
  </form>
</div>
