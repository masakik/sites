<button class="btn btn-sm btn-outline-warning" data-toggle="modal" data-target="#mudar-reponsavel"
  title="Mudar responsável">
  <i class="fas fa-exchange-alt"></i>
</button>

<div class="modal fade" id="mudar-reponsavel" tabindex="-1" aria-labelledby="mudar-reponsavel-label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="mudar-reponsavel-label">
          Mudar responsável
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
          <input type="hidden" name="acao" value="changeOwner">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>
