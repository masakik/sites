<div>
  @if (!empty($numero_usp))
    Administrador:
    @if (\App\Models\User::where('codpes', $numero_usp)->first())
      {{ $numero_usp }} - {{ \App\Models\User::where('codpes', $numero_usp)->first()->name }} -
      {{ \App\Models\User::where('codpes', $numero_usp)->first()->email }}
    @else
      {{ $numero_usp }} - <b>Usuário ainda não fez login</b>
    @endif

    @can('sites.update', $site)
      <form method="POST" action="sites/{{ $site->id }}" style="display:inline" class="delete-form">
        @csrf
        @method('patch')
        <input type="hidden" name="codpes" value="{{ $numero_usp }}">
        <input type="hidden" name="acao" value="deleteAdmin">
        <button type="submit" class="delete-item btn btn-sm text-danger"><i class="fas fa-trash-alt"></i></button>
      </form>
    @endcan
  @endif
</div>
