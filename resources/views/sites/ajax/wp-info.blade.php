<div><b>Options</b></div>

<div>Modo manutenção: {{ $wp->core['maintenance_mode'] }}</div>
Versão PHP: {{ $wp->cli['php_version'] }}<br>
siteurl: {{ $wp->options('siteurl') }}<br>
home: {{ $wp->options('home') }}<br>
admin_email: {{ $wp->options('admin_email') }}<br>

<div class="mt-2"><b>Core</b></div>
<div>Core: {{ $wp->core['version'] }}
  (Updates: {{ $wp->coreUpdates() }})
</div>

{{-- @dd($wp) --}}
<div class="mt-2 form-inline">
  <b>Plugins</b>
  @include('sites.ajax.partials.plugin-install-btn')
</div>
<table class="table table-sm table-stripped table-hover">
  @foreach ($wp->plugins as $plugin)
    <tr>
      <td>{{ $plugin['name'] }}</td>
      <td class="form-inline">
        {{ $plugin['status'] }} 
        @include('sites.ajax.partials.plugin-activate-deactivate-btn')
        @include('sites.ajax.partials.plugin-delete-btn')
      </td>
      <td>{{ $plugin['update'] }}</td>
      <td>{{ $plugin['version'] }}</td>
    </tr>
  @endforeach
</table>

<div class="mt-2"><b>Themes</b></div>
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
