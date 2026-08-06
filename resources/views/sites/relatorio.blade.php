@extends('layouts.app')

@section('styles')
  @parent
  <style>
    .relatorio-table-wrapper {
      max-width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .relatorio-table {
      min-width: 1500px;
    }

    .relatorio-table th,
    .relatorio-table td {
      white-space: nowrap;
    }
  </style>
@endsection

@section('content')
  @parent

  <div class="card">
    <div class="card-header h4">Relatório gerencial</div>
    <div class="card-body">
      <div class="table-responsive relatorio-table-wrapper">
        <table
          class="table table-striped datatable-simples dt-buttons dt-buttons-pdf-landscape dt-fixed-header relatorio-table">
          <thead>
            <tr>
              <th scope="col">Site</th>
              <th scope="col">Responsável</th>
              <th scope="col">Categoria</th>
              <th scope="col">Gerenciador</th>
              <th scope="col">Servidor/porta</th>
              <th scope="col">Path</th>
              <th scope="col">Status do gerenciador</th>
              <th scope="col">Status do site</th>
              <th scope="col">Login remoto</th>
              <th scope="col">Usuários remotos</th>
              <th scope="col">Versão WordPress</th>
              <th scope="col">Plugins ativos</th>
              <th scope="col">Versão PHP</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($rows as $row)
              <tr>
                <td title="{{ $row['url'] }}">
                  <a href="{{ route('sites.show', $row['site']) }}">
                    {{ $row['short_url'] }}
                  </a>
                </td>
                <td>{{ $row['site']->ownerName }}</td>
                <td>{{ $row['site']->categoria }}</td>
                <td>{{ $row['manager'] }}</td>
                <td title="{{ $row['host'] }}/{{ $row['port'] }}">
                  {{ $row['short_host'] }}/{{ $row['port'] }}
                </td>
                <td>{{ $row['path'] }}</td>
                <td>
                  <span class="d-none">{{ $row['manager_status']['value'] }}</span>
                  <i class="{{ $row['manager_status']['icon'] }}" title="{{ $row['manager_status']['title'] }}"></i>
                </td>
                <td>{{ $row['site']->status }}</td>
                <td>
                  {{ $row['remote_login'] }}
                </td>
                <td title="{{ $row['users'] }}">{{ $row['users'] ?: '-' }}</td>
                <td>{{ $row['wordpress_version'] }}</td>
                <td title="{{ $row['active_plugins'] }}">{{ $row['active_plugins'] ?: '-' }}</td>
                <td>{{ $row['php_version'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
