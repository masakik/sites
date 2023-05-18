<form method="POST" action="sites/{{ $site->id }}/wp-plugin">
  @csrf
  <input type="hidden" name="plugin_name" value="{{ $plugin['name'] }}">
  @if ($plugin['status'] == 'active')
    <input type="hidden" name="acao" value="deactivate">
    <button class="btn btn-sm btn-link">desativar</button>
  @else
    <input type="hidden" name="acao" value="activate">
    <button class="btn btn-sm btn-link pr-1">ativar</button>
  @endif
</form>
