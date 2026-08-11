@extends('layouts.app')

@section('content')
  @parent

  <div class="card">
    <div class="card-header h4">Relatório gerencial</div>
    <div class="card-body p-2">
      <div>
        <table
          class="table table-sm table-striped small datatable-simples dt-buttons dt-buttons-pdf-landscape dt-fixed-header">
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
              <th scope="col">WordPress</th>
              <th scope="col">Plugins ativos</th>
              <th scope="col">PHP</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($rows as $row)
              <tr>
                <td title="{{ $row['url'] }}">
                  <a class="d-block text-truncate" href="{{ route('sites.show', $row['site']) }}">
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
                <td>
                  @if ($row['users_count'])
                    <button class="btn btn-link btn-sm p-0 text-left" type="button" data-toggle="collapse"
                      data-target="#usuarios-{{ $row['site']->id }}" aria-expanded="false"
                      aria-controls="usuarios-{{ $row['site']->id }}">
                      Ver usuários ({{ $row['users_count'] }})
                    </button>
                    <div class="collapse mt-1" id="usuarios-{{ $row['site']->id }}">
                      <div class="card card-body p-2 small">{{ $row['users'] }}</div>
                    </div>
                  @else
                    -
                  @endif
                </td>
                <td>{{ $row['wordpress_version'] }}</td>
                <td>
                  @if ($row['active_plugins_count'])
                    <button class="btn btn-link btn-sm p-0 text-left" type="button" data-toggle="collapse"
                      data-target="#plugins-{{ $row['site']->id }}" aria-expanded="false"
                      aria-controls="plugins-{{ $row['site']->id }}">
                      Ver plugins ({{ $row['active_plugins_count'] }})
                    </button>
                    <div class="collapse mt-1" id="plugins-{{ $row['site']->id }}">
                      <div class="card card-body p-2 small">{{ $row['active_plugins'] }}</div>
                    </div>
                  @else
                    -
                  @endif
                </td>
                <td>{{ $row['php_version'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
