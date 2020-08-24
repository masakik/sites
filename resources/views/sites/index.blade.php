@extends('master')

@section('content_header')
@stop

@section('content')
@parent

<form method="get" action="/sites">
  <div class="row">
    <div class="input-group">

    <input type="text" class="form-control" placeholder="Domínio ..." name="dominio" value="{{ Request()->dominio }}">

    <select class="custom-select" id="status" name="status">
        <option value="aprovado" @if(Request()->status=="aprovado") selected @endif> 
            Aprovado
        </option>
        <option value="solicitado" @if(Request()->status=="solicitado") selected @endif>
            Solicitado (ainda não aprovado)
        </option>
    </select>

      <span class="input-group-btn">
        <button type="submit" class="btn btn-success"> Buscar </button>
      </span>

    </div>
  </div><!-- /input-group -->
</form>

<br>
{{ $sites->links() }}

<div class="row">
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
@foreach ($sites as $site)
    @include('sites/partials/site')
@endforeach
</tbody>
</table>
{{ $sites->links() }}
</div>
</div>

@stop