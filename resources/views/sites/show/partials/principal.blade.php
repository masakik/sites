Categoria: <b>{{ $site->categoria }}</b>
@can('admin')
  <div>
    Gerenciador: <b>{{ $site->config['manager'] ?? '-' }}</b>
  </div>
@endcan
<div>
  Justificativa: {!! nl2br($site->justificativa) !!}
</div>
