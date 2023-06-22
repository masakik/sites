<form method="POST" action="sites/{{ $site->id }}/gerenciador">
  @csrf
  <input type="hidden" name="acao" value="refresh">
  <button class="btn btn-sm btn-outline-info"><i class="fas fa-sync"></i></button>
</form>
