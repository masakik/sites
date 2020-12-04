@extends('master')

@section('title', 'Dashboard')

@section('content_header')
@stop

@section('content')
@parent

@section('styles')
@parent
<style>
    table {
        table-layout: fixed;
        word-wrap: break-word;
    }
</style>
@stop

<form method="get" action="/chamados">
  <div class="row">
    <div class="input-group">
    <input type="text" class="form-control" placeholder="Buscar ..." name="search" value="{{ Request()->search }}">
    <select class="custom-select" id="status" name="status">
      <option value="" selected>
          Status do Chamado
        </option>
    @foreach (App\Models\Chamado::status() as $status)
      <option value="{{ $status }}" @if(Request()->status=="$status") selected @endif>
          {{$status}}
        </option>
    @endforeach
    </select>
      <span class="input-group-btn">
        <button type="submit" class="btn btn-success"> Buscar </button>
      </span>
    </div>
  </div>
</form>

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th style="width: 50%">Site</th>
        <th style="width: 50%">Chamado</th>
      </tr>
    </thead>

    <tbody>

@forelse ($chamados->sortByDesc('created_at') as $chamado)
      <tr>
        <td> 
            <ul style="list-style-type: none;">
              <li> <b>id:</b> {{ $chamado->id }} </li>
              <li> <b>site:</b>{{ $chamado->site->dominio.config('sites.dnszone') }}</li>
              <li> <b>por:</b> {{ $chamado->user->name }}</li>
              <li> <b>em:</b> {{ Carbon\Carbon::parse($chamado->created_at)->format('d/m/Y H:i') }}</li>
              <li> <b>status:</b>{{ $chamado->status }}</li>
              <li> <b>tipo:</b> {{ $chamado->tipo }}</li>
           </ul>
        </td>
        <td><a href="/chamados/{{$chamado->site_id}}/{{$chamado->id}}">{{ strip_tags($chamado->descricao) }}</a></td>
      </tr>
@empty
    <tr>
        <td colspan="7">Não há chamados abertos</td>
    </tr>
@endforelse
</tbody>
</table>

</div>


@stop
