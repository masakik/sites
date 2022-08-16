Categoria: <b>{{ $site->categoria }}</b>
@can('admin')
  <div>
    Servidor: {{ json_encode($site->config, JSON_UNESCAPED_UNICODE) }}
  </div>
@endcan
<div>
  Justificativa: {!! nl2br($site->justificativa) !!}
</div>
