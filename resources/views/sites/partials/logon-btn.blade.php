@if ($site->status != 'Solicitado' && $site->status != 'Aprovado - Em Processamento')

  @if ($site->config['manager'] == 'wordpress')
    <form method="post" target="_blank" action="sites/{{ $site->id }}/login">
      @csrf
      @if ($site->config['remoteLogin'])
        <button class="btn btn-sm btn-outline-success" title="Login remoto">
          <i class="fas fa-tools"></i>
        </button>
      @else
        <button class="btn btn-sm btn-outline-success p-0" title="Login remoto pode não estar disponível!">
          <span class="fa-stack fa-1x">
            <i class="fas fa-tools fa-stack-1x"></i>
            <i class="fas fa-ban fa-2x fa-stack-1x text-danger"></i>
          </span>
        </button>
      @endif
    </form>
  @endif

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
    <a href="https://{{ $site->url }}{{ $port }}{{ $loginpath }}" class="btn btn-sm btn-outline-success"
      target="_blank" title="Logon">
      <i class="fas fa-sign-in-alt"></i>
    </a>
  @endif

@endif
