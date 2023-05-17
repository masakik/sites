<div class="card">
  <div class="card-header py-1">
    Gerenciador
    @include('sites.partials.config-btn')
  </div>
  <div class="card-body py-1">
    <div>Gerenciador: <b>{{ $site->config['manager'] }}</b></div>
    <div>Host: <b>{{ $site->config['host'] }}</b> porta: <b>{{ $site->config['port'] ?? '' }}</b> </div>
    <div>Path: <b>{{ $site->config['path'] }}</b></div>
    {{-- <div>Usuário dono: {{ $site->config['suUser'] ?? '' }}</div> --}}
  </div>
</div>

@if ($site->config['manager'] == 'wordpress')
  @include('sites.show.partials.wordpress')
@endif
