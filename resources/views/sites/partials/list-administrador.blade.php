<div class="ml-3">
  @if (!empty($numero_usp))
    @can('sites.update', $site)
      <form method="POST" action="sites/{{ $site->id }}" style="display:inline" class="delete-form">
        @csrf
        @method('patch')
        <input type="hidden" name="deleteadmin" value="{{ $numero_usp }}">
        <button type="submit" class="delete-item btn btn-sm text-danger"><i class="fas fa-trash-alt"></i></button>
      </form>
    @endcan

    @if (\App\Models\User::where('codpes', $numero_usp)->first())
      {{ $numero_usp }} - {{ \App\Models\User::where('codpes', $numero_usp)->first()->name }} -
      {{ \App\Models\User::where('codpes', $numero_usp)->first()->email }}
    @else
      {{ $numero_usp }} - <b>Usuário ainda não fez login</b>
    @endif
  @endif
</div>
