<div class="card mt-3">
  <div class="card-header py-1">
    <i class="fas fa-users"></i> Pessoas
    @includeWhen(Gate::check('sites.update', $site), 'sites.partials.pessoas-add-admin')
  </div>
  <div class="card-body py-1">
    <div>
      <b>Responsável:</b> {{ $site->owner }} - {{ $site->ownerName }} - {{ $site->ownerEmail }}
      @includeWhen(Gate::check('sites.update', $site), 'sites.partials.pessoas-change-owner')
    </div>
    <div class="ml-3">
      @foreach ($site->administrators as $administrator)
        @include('sites.partials.list-administrador', ['administrator' => $administrator])
      @endforeach
    </div>
  </div>
</div>
