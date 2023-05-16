<button class="btn btn-link btn-sm" data-toggle="modal" data-target="#exampleModal">
  <i class="fas fa-edit"></i>
</button>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLabel">
          Config
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="sites/{{ $site->id }}">
        <div class="modal-body">
          @csrf
          @method('patch')
          <input type="hidden" name="acao" value="config">
          <x-input.text name="host" label="Host" value="{{ $site->config['host'] }}" />
          <x-input.text type="number" name="port" label="Porta" value="{{ $site->config['port'] }}" />
          <x-input.text name="path" label="Path" value="{{ $site->config['path'] }}" />
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
          <button type="submit" class="btn btn-primary">Salvar mudanças</button>
        </div>
      </form>
    </div>
  </div>
</div>
