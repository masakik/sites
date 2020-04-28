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

<div class="card">
  <div class="card-header"><b>Relação dos sites da FFLCH</b></div>
<div class="card-body">
@inject('pessoa','Uspdev\Replicado\Pessoa')
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
              <a href="http://{{$site->dominio.config('sites.dnszone')}}">
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
