<form method="POST" action="sites/{{ $site->id }}/gerenciador">
  @csrf
  <button class="btn btn-sm btn-link"><i class="fas fa-sync"></i></button>
  <input type="hidden" name="acao" value="refresh">
</form>
