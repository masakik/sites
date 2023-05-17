@if ($site->config['manager'] == 'drupal')
  @if (config('app.env') == 'production')
    @php
      $port = '';
      $loginpath = '/loginbytoken/?temp_token=' . auth()->user()->temp_token . '&codpes=' . Auth::user()->codpes;
    @endphp
  @else
    @php
      $port = ':8088';
      $loginpath = '/loginbytoken/?temp_token=' . auth()->user()->temp_token . '&codpes=' . Auth::user()->codpes;
    @endphp
  @endif
@else
  @php($port = $loginpath = '')
@endif

@if ($site->status != 'Solicitado' && $site->status != 'Aprovado - Em Processamento')
  <a href="https://{{ $site->url }}{{ $port }}{{ $loginpath }}"
    class="btn btn-sm btn-outline-success" target="_blank" title="Logon">
    <i class="fas fa-sign-in-alt"></i>
  </a>
@endif
