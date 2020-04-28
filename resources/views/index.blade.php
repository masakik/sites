@extends('master')

@section('content_header')
    <h1>Sites</h1>
@stop

@section('content')
    @parent
    @auth <script>window.location = "/sites";</script> @endauth

    <div class="row">
      <div class="col-sm">
        <a href="/senhaunica/login"> Faça login </a> com a senha única USP para
        administrar seu site. 
      </div>
    </div>
<br>

@inject('pessoa','Uspdev\Replicado\Pessoa')

<div class="card">
  <div class="card-header"><b>Relação dos sites da FFLCH</b></div>
<div class="card-body">

Total de sites existentes: <b>{{ $sites->where('status','aprovado')->count() }}</b> <br>
Total de sites ainda não aprovados: <b>{{ $sites->where('status','solicitado')->count() }} </b>

<br><br>
<table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">Site</th>
      <th scope="col">Responsável</th>
      <th scope="col">status</th>
    </tr>
  </thead>
  <tbody>
    @foreach($sites->sortBy('dominio') as $site)
        <tr>
          <td> 
            @if($site->status == 'aprovado')
              <a target="_blank" href="http://{{$site->dominio.config('sites.dnszone')}}">
                {{$site->dominio.config('sites.dnszone')}}
              </a>
            @else
              {{$site->dominio.config('sites.dnszone')}}
            @endif
          </td>
          <td>{{ $pessoa::dump($site->owner)['nompes'] }}</td>
          <td>{{ $site->status }}</td>
        </tr>
    @endforeach
  </tbody>
</table>
</div>
</div>

@endsection('content')
