<div><b>Options</b></div>

<div>Modo manutenção: {{ $wp->core['maintenance_mode'] }}</div>
Versão PHP: {{ $wp->cli['php_version'] }}<br>
wp_cli_version: {{ $wp->cli['wp_cli_version'] }}<br>
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
{{-- @dd($wp->sites) --}}
<table class="table table-sm table-stripped table-hover">
  @foreach ($wp->plugins as $plugin)
    <tr>
      <td>{{ $plugin['name'] }}</td>
      <td class="form-inline">
        {{ $plugin['status'] }}
        @include('sites.ajax.partials.plugin-actions-btn')
      </td>
      <td>{{ $plugin['update'] }}</td>
      <td>{{ $plugin['version'] }}</td>
    </tr>
  @endforeach
</table>

<div class="mt-2"><b>Themes</b></div>
<table class="table table-sm table-stripped table-hover">
  @foreach ($wp->themes as $theme)
    <tr>
      <td>{{ $theme['name'] }}</td>
      <td>{{ $theme['status'] }}</td>
      <td>{{ $theme['update'] }}</td>
      <td>{{ $theme['version'] }}</td>
    </tr>
  @endforeach
</table>

<div class="mt-2"><b>Usuários</b></div>
<table class="table table-sm table-stripped table-hover">
  @foreach ($wp->users as $user)
    <tr>
      <td>{{ $user['user_login'] }}</td>
      <td>{{ $user['user_email'] }}</td>
      <td>{{ $user['user_registered'] }}</td>
      <td>{{ $user['roles'] }}</td>
    </tr>
  @endforeach
</table>

<div class="mt-2"><b>Sites (log de execução)</b></div>
<table class="table table-sm table-stripped table-hover">
  @foreach ($wp->sites as $k => $v)
    <tr>
      <td>{{ $k }}</td>
      <td>{{ $v }}</td>
    </tr>
  @endforeach
</table>
