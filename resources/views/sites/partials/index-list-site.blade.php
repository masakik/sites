<div class="h5">
  <a href="sites/{{ $site->id }}">{{ $site->dominio }}{{ config('sites.dnszone') }}</a>
  @can('sites.update', $site)
    <a href="sites/{{ $site->id }}/edit"> <i class="fas fa-edit"></i> </a>
  @endcan
</div>

<div>
  <b>categoria: </b>{{ $site->categoria }}
</div>

<div>
  <b>status: </b>
  @if ($site->status == 'Solicitado')
    Aguardando aprovação
  @else
    {{ $site->status }}
  @endif
</div>

@can('admin')
  <div>
    Servidor: {{ json_encode($site->config, JSON_UNESCAPED_UNICODE) }}
  </div>
@endcan

<div>
  <b>chamados: </b>
  <a href="chamados/{{ $site->id }}">
    {{ $site->chamados->where('status', 'aberto')->count() }} abertos
  </a>
  <a href="chamados/{{ $site->id }}/create" title="Novo chamado" class="btn btn-sm">
    <i class="fas fa-plus"></i>
  </a>
</div>
