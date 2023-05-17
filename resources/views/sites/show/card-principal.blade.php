<div class="card">
  <div class="card-header py-1">
    <i class="fas fa-university"></i>
    Site
    @include('sites.partials.site-edit-btn') &nbsp;
  </div>
  <div class="card-body py-1">
    <div>Host: <b>{{ $site->url }}</b></div>
    <div>Categoria: <b>{{ $site->categoria }}</b></div>
    <div>
      Justificativa: {!! nl2br($site->justificativa) !!}
    </div>
  </div>
</div>
