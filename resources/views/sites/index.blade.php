@extends('master')

@section('content_header')
@stop

@section('content')
  @parent

  @include('sites.partials.index-search')
  <br>
  <div class="row">
    {{ $sites->links() }}
    <div class="table-responsive">
      <table class="table table-sm table-striped">
        <thead>
          <tr>
            <th>Site</th>
            <th>Pessoas</th>
            <th>Ações</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($sites as $site)
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
          @endforeach
        </tbody>
      </table>
      {{ $sites->links() }}
    </div>
  </div>

@stop
