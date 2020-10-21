@section('styles')
@parent
<style>
    table {
        table-layout: fixed;
        word-wrap: break-word;
    }
</style>
@stop

<form method="get" action="/chamados">
  <div class="row">
    <div class="input-group">
    <input type="text" class="form-control" placeholder="BUSCA POR DOMÍNIO" name="busca" value="{{ Request()->busca }}">
    <select class="custom-select" id="busc_aberta" name="busc_aberta">
      <option value="" selected>
          Todos
        </option>
    @foreach ($chamado->status() as $status)
      <option value="{{ $status }}" @if(Request()->busc_aberta=="$status") selected @endif>
          Abertos
        </option>
    @endforeach
    @foreach ($chamado->fechado_em() as $fechado_em)
      <option value="{{ $fechado_em }}" @if(Request()->fechado_em=="$fechado_em") selected @endif>
          Fechados
        </option>
    @endforeach
    </select>
      <span class="input-group-btn">
        <button type="submit" class="btn btn-success"> Buscar </button>
      </span>
    </div>
  </div>
</form>
{{ $chamados->appends(request()->query())->links() }}

<h1>Chamados de {{ $site->dominio.config('sites.dnszone') }}</h1>

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th style="width: 50%">Infos</th>
        <th style="width: 50%">Chamado</th>
      </tr>
    </thead>

    <tbody>

@forelse ($site->chamados->sortByDesc('created_at') as $chamado)
      <tr>
       <td>
       <ul style="list-style-type: none;">
        <li><b>id: </b>{{ $chamado->id }}</li>
        <li><b>por: </b>{{ $chamado->user->name }}</li>
        <li><b>em: </b>{{ Carbon\Carbon::parse($chamado->created_at)->format('d/m/Y H:i') }}</li>
        <li><b>status: </b>{{ $chamado->status }}</li>
        <li><b>tipo: </b>{{ $chamado->tipo }}</li>
       </li>
      </td>
        <td><a href="/chamados/{{$chamado->site_id}}/{{$chamado->id}}">{{ $chamado->descricao }}</a></td>
      </tr>
@empty
    <tr>
        <td colspan="6">Não há chamados para esse site</td>
    </tr>
@endforelse
</tbody>
</table>

</div>

