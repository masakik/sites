@if ($plugin['status'] == 'inactive')
  <form method="POST" action="sites/{{ $site->id }}/wp-plugin">
    @csrf
    <input type="hidden" name="plugin_name" value="{{ $plugin['name'] }}">
    <input type="hidden" name="acao" value="delete">
    | <button class="btn btn-sm btn-link pl-0">apagar</button>
  </form>
@endif
