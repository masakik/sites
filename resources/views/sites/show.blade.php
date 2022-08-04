@extends('layouts.app')

@section('content')
  @parent

  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Site</th>
          <th>Pessoas</th>
          <th>Ações</th>
        </tr>
      </thead>

      <tbody>
          <tr>
            <td>
              @include('sites.partials.index-list-site')
            </td>

            <td>
              @include('sites.partials.index-list-pessoas')
            </td>

            <td>
              @include('sites.partials.index-list-acoes')
            </td>
          </tr>
        <tr>
          <td colspan="3"><b>Justificativa: </b>{!! $site->justificativa !!}</td>
        </tr>
      </tbody>
    </table>
  </div>

  @include('chamados/partials/index')
@stop
