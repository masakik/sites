@if ($chamado->status == 'aberto')
  <span class="badge badge-success">
    {{ $chamado->status }}
  </span>
@elseif($chamado->status == 'fechado')
  <span class="badge badge-secondary">
    {{ $chamado->status }}
  </span>
@endif
