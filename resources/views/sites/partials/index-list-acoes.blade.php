<ul class="list-group">

  <li class="list-group-item">
    @include('sites.partials.logon-btn')
  </li>

  @can('admin')
    <li class="list-group-item">
      <div class="form-inline">
        @if ($site->status == 'Solicitado')
          @include('sites.partials.aprovar-btn')
          &nbsp;
          @include('sites.partials.delete-btn')

        @elseif ($site->status == 'Aprovado - Habilitado')
          @include('sites.partials.desabilitar-btn')

        @elseif ($site->status == 'Aprovado - Desabilitado')
          @include('sites.partials.habilitar-btn')
          &nbsp;
          @include('sites.partials.delete-btn')
          
        @elseif ($site->status == 'Aprovado - Em Processamento')
          @include('sites.partials.voltar-btn')
        @endif
      </div>
    </li>
  @endcan

</ul>
