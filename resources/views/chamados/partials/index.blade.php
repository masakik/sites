<h1>Chamados de {{ $site->dominio.config('sites.dnszone') }}</h1>

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>#</th>
        <th>Aberto por</th>
        <th>Em</th>
        <th>Status</th>
        <th>Tipo</th>
        <th>Descrição</th>
      </tr>
    </thead>

    <tbody>

@forelse ($site->chamados->sortByDesc('created_at') as $chamado)
      <tr>
        <td>{{ $chamado->id }}</td>
        <td>{{ $chamado->user->name }}</td>
        <td>{{ Carbon\Carbon::parse($chamado->created_at)->format('d/m/Y H:i') }}</td>
        <td><b>{{ $chamado->status }}</b></td>
        <td>{{ $chamado->tipo }}</td>
        <td><a href="/chamados/{{$chamado->site_id}}/{{$chamado->id}}">{!! $chamado->descricao !!}</a></td>
      </tr>
@empty
    <tr>
        <td colspan="5">Não há chamados para esse site</td>
    </tr>
@endforelse
</tbody>
</table>

</div>

