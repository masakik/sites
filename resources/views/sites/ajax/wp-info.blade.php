<div><b>WP Options</b></div>
<div>Core: {{ $wp->core['version'] }}
  (Updates: {{ $wp->coreUpdates() }})
</div>
<div>Modo manutenção: {{ $wp->core['maintenance_mode'] }}</div>
Versão PHP: {{ $wp->cli['php_version'] }}<br>
siteurl: {{ $wp->options('siteurl') }}<br>
home: {{ $wp->options('home') }}<br>
admin_email: {{ $wp->options('admin_email') }}<br>


{{-- @dd($wp) --}}
<br>
<div><b>Plugins</b></div>
<table class="table table-sm table-stripped table-hover">
  @foreach ($wp->plugins as $plugin)
    <tr>
      <td>{{ $plugin['name'] }}</td>
      <td>{{ $plugin['status'] }}</td>
      <td>{{ $plugin['update'] }}</td>
      <td>{{ $plugin['version'] }}</td>
    </tr>
  @endforeach
</table>

<div><b>Themes</b></div>
<table class="table table-sm table-stripped table-hover">
  @foreach ($wp->themes as $plugin)
    <tr>
      <td>{{ $plugin['name'] }}</td>
      <td>{{ $plugin['status'] }}</td>
      <td>{{ $plugin['update'] }}</td>
      <td>{{ $plugin['version'] }}</td>
    </tr>
  @endforeach
</table>

{{-- <div><b>Uspdev Senhaunica WP</b></div>
{{ $wp->uspdevSenhaunicaWP() }} --}}
