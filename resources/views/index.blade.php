@extends('layouts.app')

@section('menu')
  {{-- sem menu se não logado --}}
@endsection

@section('content')
  @parent

  <div class="card">
    <div class="card-header h4">Relação de sites</div>
    <div class="card-body">
      <div>
        Total de sites existentes: <b>{{ $sites->where('status', '!=', 'Solicitado')->count() }}</b> <br>
        Total de sites ainda não aprovados: <b>{{ $sites->where('status', 'Solicitado')->count() }} </b>
      </div>

      <table class="table table-striped mt-3">
        <thead>
          <tr>
            <th scope="col">Site</th>
            <th scope="col">Responsável</th>
            <th scope="col">Categoria</th>
            <th scope="col">Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($sites as $site)
            <tr>
              <td>
                @if ($site->status == 'Aprovado - Habilitado')
                  <a target="{{ config('sites.dnszone') }}" href="http://{{ $site->url }}">
                    {{ $site->url }}
                  </a>
                @else
                  {{ $site->url }}
                @endif
              </td>
              <td>{{ $site->ownerName }}</td>
              <td>{{ $site->categoria }}</td>
              <td>{{ $site->status }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection
