<div class="card mt-3">
  <div class="card-header py-1">
    <i class="fas fa-users"></i> Pessoas
    @includeWhen(['sites.update', $site],'sites.partials.pessoas-add-admin')
  </div>
  <div class="card-body py-1">
    <div>
      <b>Responsável:</b> {{ $site->owner }} - {{ $site->ownerName }} - {{ $site->ownerEmail }}
      @includeWhen(Gate::check('sites.update', $site), 'sites.partials.pessoas-change-owner')

      @foreach (explode(',', $site->numeros_usp) as $numero_usp)
        @include('sites.partials.list-administrador')
      @endforeach
    </div>
  </div>
</div>
