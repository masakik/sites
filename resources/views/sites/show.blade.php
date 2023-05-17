@extends('layouts.app')

@section('content')
  @parent

  <div class="card">
    <div class="card-header h4 form-inline py-1">
      Site {{ $site->dominio }} &nbsp;
      @include('sites.partials.status-badge') &nbsp;
      @includeWhen($site->config['manager'] == 'drupal', 'sites.partials.logon-btn') &nbsp;
      @includeWhen(Gate::check('admin'), 'sites.partials.admin-btns')
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          @include('sites.show.card-principal')
          @include('sites.show.card-pessoas')
        </div>
        <div class="col-md-6">
          @include('sites.show.card-gerenciador')
        </div>
      </div>

      @if (config('sites.chamados') == 'local')
        <div class="row mt-3">
          <div class="col-md-12">
            <div class="h5">
              Chamados
              <span class="badge badge-primary">
                {{ $site->chamados->where('status', 'aberto')->count() }} abertos
              </span>
              <a href="chamados/{{ $site->id }}/create" title="Novo chamado"
                class="btn btn-sm btn-outline-primary py-0">
                <i class="fas fa-plus"></i>
              </a>
            </div>
            @include('chamados/partials/index')
          </div>
        </div>
      @endif
    </div>
  </div>
@endsection
