@section('styles')
  @parent
  <style>
    table {
      table-layout: fixed;
      word-wrap: break-word;
    }
  </style>
@stop

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th style="width: 50%">Infos</th>
        <th style="width: 50%">Descrição</th>
      </tr>
    </thead>

    <tbody>
      @forelse ($site->chamados->sortByDesc('created_at') as $chamado)
        <tr>
          <td>
            <ul style="list-style-type: none;">
              <li>
                <b>#</b>{{ $chamado->id }} 
                | <b>tipo: </b>{{ $chamado->tipo }}
                @include('chamados.partials.status-badge')
              </li>
              <li>
                <b>em: </b>{{ Carbon\Carbon::parse($chamado->created_at)->format('d/m/Y H:i') }},
                <b>por: </b>{{ $chamado->user->name }}
              </li>
              <li></li>
            </ul>
          </td>
          <td>
            <a href="{{ route('chamados.show', $chamado) }}">{!! strip_tags($chamado->descricao) !!}</a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6">Não há chamados para esse site</td>
        </tr>
      @endforelse
    </tbody>
  </table>

</div>
