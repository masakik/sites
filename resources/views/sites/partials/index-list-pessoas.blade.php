<div>
  <b>Responsável:</b> {{ $site->owner }} - {{ $site->ownerName }} - {{ $site->ownerEmail }}
</div>
<div class="ml-3">
  @foreach ($site->administrators as $administrator)
    @include('sites.partials.list-administrador', ['administrator' => $administrator])
  @endforeach
</div>
