<button class="btn btn-link btn-sm" data-toggle="modal" data-target="#administrador-add">
  <i class="fas fa-user-plus"></i>
</button>

<div class="modal fade" id="administrador-add" tabindex="-1" aria-labelledby="administradorAddLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="administradorAddLabel">
          Adicionar administrador
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="sites/{{ $site->id }}">
        <div class="modal-body">
          @csrf
          @method('patch')
          <x-input.select-pessoa />
          <input type="hidden" name="acao" value="addAdmin">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
          <button type="submit" class="btn btn-primary">Salvar mudanças</button>
        </div>
      </form>
    </div>
  </div>
</div>
