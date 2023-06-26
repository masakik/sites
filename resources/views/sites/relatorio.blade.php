@extends('layouts.app')

@section('content')
  @parent

  <div class="card">
    <div class="card-header h4">Relatório gerencial</div>
    <div class="card-body">
      <table class="table table-striped datatable-simples dt-fixed-header responsive">
        <thead>
          <tr>
            <th scope="col">Site</th>
            <th scope="col">Responsável</th>
            <th scope="col">Categoria</th>
            <th scope="col">Gerenciador</th>
            <th scope="col">Servidor/porta</th>
            <th scope="col">Path</th>
            <th scope="col">Status</th>
            <th scope="col">Remote login</th>
            {{-- <th scope="col">Config</th> --}}
            <th scope="col">Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($sites as $site)
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
              <td>
                {{ $site->config['remoteLogin'] ?? '-' }}
              </td>
              <td>{{ $site->status }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection
