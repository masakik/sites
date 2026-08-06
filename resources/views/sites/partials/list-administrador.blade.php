<div>
  Administrador:
  @if ($administrator['name'])
    {{ $administrator['codpes'] }} - {{ $administrator['name'] }} - {{ $administrator['email'] }}
  @else
    {{ $administrator['codpes'] }} - <b>Usuário ainda não fez login</b>
  @endif

  @can('sites.update', $site)
    <form method="POST" action="sites/{{ $site->id }}" style="display:inline" class="delete-form">
      @csrf
      @method('patch')
      <input type="hidden" name="codpes" value="{{ $administrator['codpes'] }}">
      <input type="hidden" name="acao" value="deleteAdmin">
      <button type="submit" class="delete-item btn btn-sm text-danger"><i class="fas fa-trash-alt"></i></button>
    </form>
  @endcan
</div>
