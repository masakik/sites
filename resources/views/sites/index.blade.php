@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
@parent

    <form method="get" action="/sites">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Domínio ..." name="dominio">
            <span class="input-group-btn">
                <button type="submit" class="btn btn-success"> Buscar </button>
            </span>
        </div><!-- /input-group -->
    </form>

<br>
{{ $sites->links() }}

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

</div>

@stop
