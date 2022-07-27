<form method="POST" action="sites/{{ $site->id }}">
  @csrf
  @method('patch')
  <input type="hidden" name="voltar_solicitacao" value="voltar_solicitacao">
  <button type="submit" class="btn btn-secondary">Voltar Solicitação</button>
</form>
