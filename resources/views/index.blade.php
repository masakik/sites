@extends('layouts.app')

@if (!auth()->check())
  @section('menu')
    {{-- sem menu se não logado --}}
  @endsection
@endif

@section('content')
  @parent

  <div class="card">
    <div class="card-header h4">Relação de sites</div>
    <div class="card-body">
      <table class="table table-striped mt-3 datatable-simples dt-fixed-headers responsive">
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
