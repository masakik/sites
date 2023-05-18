@switch($site->status)
  @case('Solicitado')
    <span class="badge badge-primary">
      Aguardando aprovação
    </span>
  @break

  @case('Aprovado - Desabilitado')
    <span class="badge badge-warning">
      Desabilitado
    </span>
  @break

  @case('Aprovado - Habilitado')
    <span class="badge badge-success">
      Habilitado
    </span>
  @break

  @default
    <span class="badge badge-secondary">
      {{ $site->status }}
    </span>
@endswitch
