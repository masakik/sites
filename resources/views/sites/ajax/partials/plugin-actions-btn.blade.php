<form method="POST" action="sites/{{ $site->id }}/wp-plugin">
  @csrf
  <input type="hidden" name="plugin_name" value="{{ $plugin['name'] }}">
  @if ($plugin['status'] == 'active')
    <input type="hidden" name="acao" value="deactivate">
    <button class="btn btn-sm btn-link text-warning">desativar</button>
  @elseif ($plugin['status'] == 'inactive')
    <input type="hidden" name="acao" value="activate">
    <button class="btn btn-sm btn-link pr-0">ativar</button> |
  @endif
</form>

@if ($plugin['status'] == 'inactive' || $plugin['status'] == 'must-use')
  <form method="POST" action="sites/{{ $site->id }}/wp-plugin">
    @csrf
    <input type="hidden" name="plugin_name" value="{{ $plugin['name'] }}">
    <input type="hidden" name="acao" value="delete">
    <button class="btn btn-sm btn-link pl-1 text-danger confirm">apagar</button>
  </form>
@endif
