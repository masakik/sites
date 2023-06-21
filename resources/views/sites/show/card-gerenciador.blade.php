<div class="card">
  <div class="card-header py-1">
    <i class="fas fa-house-user"></i>
    Gerenciador
    @includeWhen(Gate::allows('admin'),'sites.partials.config-btn')
  </div>
  <div class="card-body py-1">
    <div>
      Gerenciador: <b>{{ $site->config['manager'] }}</b>
    </div>
    <div>Host: <b>{{ $site->config['host'] }}</b> porta: <b>{{ $site->config['port'] ?? '' }}</b> </div>
    <div>Path: <b>{{ $site->config['path'] }}</b></div>
    <div id="gerenciador-data-insert" class="ml-3"></div>
  </div>
</div>

@if ($site->config['manager'] == 'wordpress')
  @include('sites.show.card-wordpress')
@endif
@if ($site->config['manager'] == 'html')
  @include('sites.show.card-html')
@endif