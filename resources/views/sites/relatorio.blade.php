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
        <table class="table table-striped datatable-simples dt-buttons dt-buttons-pdf-landscape dt-fixed-header relatorio-table">
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
          @foreach ($sites as $site)
            @php
              $wp = $wordpress[$site->id] ?? null;
              $wpUsers = collect($wp->users ?? [])
                  ->map(function ($user) {
                      $login = $user['user_login'] ?? '';
                      $roles = $user['roles'] ?? '';
                      $roles = is_array($roles) ? implode(', ', $roles) : $roles;

                      return $login . ($roles ? ' (' . $roles . ')' : '');
                  })
                  ->filter()
                  ->implode(', ');
              $activePlugins = collect($wp->plugins ?? [])
                  ->where('status', 'active')
                  ->pluck('name')
                  ->filter()
                  ->implode(', ');
            @endphp
            <tr>
              <td title="{{ $site->url }}">
                <a href="{{ route('sites.show', $site) }}">
                  {{ Illuminate\Support\Str::limit($site->url, 15) }}
                </a>
              </td>
              <td>{{ $site->ownerName }}</td>
              <td>{{ $site->categoria }}</td>
              <td>{{ $site->config['manager'] }}</td>
              <td title="{{ $site->config['host'] }}/{{ $site->config['port'] }}">

                {{ Illuminate\Support\Str::limit($site->config['host'], 15) }}/{{ $site->config['port'] }}
              </td>
              <td>{{ $site->config['path'] }}</td>
              <td>
                <span class="d-none">{{ $site->config['status'] }}</span>
                @if ($site->config['status'] == 'erro')
                  <i class="fas fa-exclamation-circle text-warning"
                    title="{{ Illuminate\Support\Str::limit($site->config['statusMsg'], 200) }}"></i>
                @elseif ($site->config['status'] == '?')
                  <i class="fas fa-question-circle text-secondary" title="Não verificado"></i>
                @else
                  <i class="fas fa-check-circle text-success" title="Parece estar tudo certo"></i>
                @endif
              </td>
              <td>{{ $site->status }}</td>
              <td>
                {{ $site->config['remoteLogin'] ?? '-' }}
              </td>
              <td title="{{ $wpUsers }}">{{ $wpUsers ?: '-' }}</td>
              <td>{{ $wp->core['version'] ?? '-' }}</td>
              <td title="{{ $activePlugins }}">{{ $activePlugins ?: '-' }}</td>
              <td>{{ $wp->cli['php_version'] ?? '-' }}</td>
            </tr>
          @endforeach
        </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
