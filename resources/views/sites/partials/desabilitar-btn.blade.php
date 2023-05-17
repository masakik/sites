<form method="POST" action="sites/{{ $site->id }}/disable">
  @csrf
  <button type="submit" class="btn btn-sm btn-info">Desabilitar</button>
</form>
