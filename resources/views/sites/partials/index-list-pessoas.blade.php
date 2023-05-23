<div>
  <b>Responsável:</b> {{ $site->owner }} - {{ $site->ownerName }} - {{ $site->ownerEmail }}
</div>
<div class="ml-3">
  @foreach (explode(',', $site->numeros_usp) as $numero_usp)
    @include('sites.partials.list-administrador')
  @endforeach
</div>
