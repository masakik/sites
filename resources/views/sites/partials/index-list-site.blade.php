<div class="h5">
  <a href="sites/{{ $site->id }}">{{ $site->dominio }}{{ config('sites.dnszone') }}</a>
  @include('sites.partials.status-badge')
</div>
<div class="ml-3"><b>categoria: </b>{{ $site->categoria }}</div>

@can('admin')
  <div class="ml-3">
    <b class=""><i class="fas fa-lock text-danger"></i></b>
    {{ $site->config['manager'] }}
    | {{ $site->short_host }}
    | {{ $site->config['port'] }}
    | {{ $site->config['path'] }}
    | <i class="{{ $site->manager_status['icon'] }}" title="{{ $site->manager_status['title'] }}"></i>
  </div>
@endcan
@if ($hasLocalTickets)
  <div>
    <b>chamados: </b>
    <a href="sites/{{ $site->id }}">
      {{ $site->open_chamados_count }} abertos
    </a>
    <a href="chamados/{{ $site->id }}/create" title="Novo chamado" class="btn btn-sm">
      <i class="fas fa-plus"></i>
    </a>
  </div>
@endif
