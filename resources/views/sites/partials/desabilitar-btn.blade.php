<form method="POST" action="sites/{{ $site->id }}/disable">
  @csrf
  {{-- <span class="fa-stack">
    <i class="fas fa-camera fa-stack-1x"></i>
    <i class="fas fa-ban fa-stack-1x" style="color:Tomato"></i>
  </span> --}}
  <button type="submit" class="btn btn-sm btn-info">Desabilitar</button>
</form>
