<div class="card">
  <div class="card-header py-1">
    <i class="fas fa-university"></i>
    Site
    @includeWhen(Gate::allows('admin'), 'sites.partials.principal-edit-btn')
    <a href="http://{{ $site->url }}" class="btn btn-sm text-info" target="{{ config('sites.dnszone') }}" title="Visitar o site">
      <i class="fas fa-share-square"></i>
    </a>
  </div>
  <div class="card-body py-1">
    <div>Host: <b>{{ $site->url }}</b></div>
    <div>Categoria: <b>{{ $site->categoria }}</b></div>
    <div>
      Justificativa: {!! nl2br($site->justificativa) !!}
    </div>
    <div>Criado em: <b>{{ $site->created_at->format('d/m/Y') }}</b></div>
  </div>
</div>
