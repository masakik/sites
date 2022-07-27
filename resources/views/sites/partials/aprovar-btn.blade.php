<form method="POST" action="sites/{{ $site->id }}">
  @csrf
  @method('patch')
  <input type="hidden" name="aprovar" value="aprovar">
  <button type="submit" class="btn btn-success">Aprovar <i class="fas fa-thumbs-up"></i></button>
</form>
