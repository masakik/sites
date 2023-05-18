<div class="h5">
  <a href="sites/{{ $site->id }}">{{ $site->dominio }}{{ config('sites.dnszone') }}</a>
  @include('sites.partials.status-badge')
</div>

<div>
  <b>categoria: </b>{{ $site->categoria }}
</div>

@can('admin')
  <div>
    <b class=""><i class="fas fa-lock text-danger"></i></b>
    {{ $site->config['manager'] }}
    | {{ $str::limit($site->config['host'], 10) }}
    | {{ $site->config['port'] }}
    | {{ $site->config['path'] }}
    | @if ($site->config['status'] == 'erro')
      <i class="fas fa-exclamation-circle text-warning"
        title="{{ Illuminate\Support\Str::limit($site->config['statusMsg'], 200) }}"></i>
    @elseif ($site->config['status'] == '?')
      <i class="fas fa-question-circle text-secondary" title="Não verificado"></i>
    @else
      <i class="fas fa-check-circle text-success" title="Parece estar tudo certo"></i>
    @endif
  </div>
@endcan
@if (config('sites.chamados') == 'local')
  <div>
    <b>chamados: </b>
    <a href="sites/{{ $site->id }}">
      {{ $site->chamados->where('status', 'aberto')->count() }} abertos
    </a>
    <a href="chamados/{{ $site->id }}/create" title="Novo chamado" class="btn btn-sm">
      <i class="fas fa-plus"></i>
    </a>
  </div>
@endif
