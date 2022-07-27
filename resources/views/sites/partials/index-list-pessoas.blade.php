<div>
  <b>Responsável:</b> {{ $site->owner }} - {{ \Uspdev\Replicado\Pessoa::dump($site->owner)['nompes'] }} -
  {{ \Uspdev\Replicado\Pessoa::email($site->owner) }}

  @can('sites.update', $site)
  <a href="sites/{{ $site->id }}/changeowner" class="btn btn-sm text-warning" title="Mudar responsável">
    <i class="fas fa-exchange-alt"></i>
  </a>
@endcan
</div>

@if (!empty($site->numeros_usp))
  <li class="list-group-item">
    <b>Administradores:</b>

    @can('sites.update', $site)
      <a href="sites/{{ $site->id }}/novoadmin" title="Adicionar administrador" class="btn btn-sm text-primary">
        <i class="fas fa-user-plus"></i>
      </a>
    @endcan

    @foreach (explode(',', $site->numeros_usp) as $numero_usp)
      @include('sites.partials.list-administrador')
    @endforeach
  </li>
@endif