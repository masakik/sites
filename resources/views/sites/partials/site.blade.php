<tr>
<td>
  <a href="/sites/{{ $site->id }}">{{ $site->dominio }}{{ config('sites.dnszone') }}</a><br>
  <b>categoria: </b>{{ $site->categoria }}<a href="/sites/{{ $site->id }}/edit"> <i class="fas fa-edit"></i> </a><br>
  <b>status: </b>
    @if ($site->status == 'solicitado') 
        Aguardando aprovação 
    @elseif ($site->status == 'aprovado') 
        Aprovado
    @endif

</td>

<td>
  <b>Responsável:</b> {{ $site->owner }} - {{ \Uspdev\Replicado\Pessoa::dump($site->owner)['nompes'] }}
  <br><br>
  <ul  class="list-group">
    
    @if(!empty($site->numeros_usp)) <li class="list-group-item"> <b>Administradores:</b> </li> @endif
    @foreach(explode(',', $site->numeros_usp) as $numero_usp)
      @if(!empty($numero_usp))
      
          <li class="list-group-item">
                    <form method="POST" action="/sites/{{ $site->id }}" style="display:inline">
                    {{csrf_field()}} {{ method_field('patch') }}
                    <input type="hidden" name="deleteadmin" value="{{ $numero_usp }}">
                    <button type="submit" class="delete-item btn"><i class="fas fa-trash-alt"></i></button>
                    </form>
{{ $numero_usp }} - {{ \Uspdev\Replicado\Pessoa::dump($numero_usp)['nompes'] }}
                       
          </li>
      @endif
    @endforeach

  </ul>
</td>

<td>
    <ul class="list-group">

        @if($site->status != "solicitado")
          <li class="list-group-item"><a href="http://{{ $site->dominio }}{{ config('sites.dnszone') }}/loginbytoken/?temp_token={{$hashlogin}}&codpes={{ Auth::user()->codpes }}" class="" target="_blank">Logon <i class="fas fa-sign-in-alt"></i></a></li>
        @endif

        @can('sites.update',$site)
           <li class="list-group-item"><a href="/sites/{{ $site->id }}/novoadmin">Adicionar administrador <i class="fas fa-user-plus"></i></a></li>
        @endcan

        @can('sites.update',$site)
            <li class="list-group-item"><a href="/sites/{{ $site->id }}/changeowner" class="">Mudar responsável </a></li>
        @endcan

        @can('admin')
            <li class="list-group-item">
                <form method="POST" action="/sites/{{ $site->id }}">
                {{csrf_field()}} {{ method_field('delete') }}
                <button type="submit" class="delete-item btn btn-danger">Deletar <i class="fas fa-trash-alt"></i></button>
                </form>
            </li>

            @if($site->status == 'solicitado')
                <li class="list-group-item">
                    <form method="POST" action="/sites/{{ $site->id }}">
                    {{csrf_field()}} {{ method_field('patch') }}
                    <input type="hidden" name="aprovar" value="aprovar">
                    <button type="submit" class="btn btn-success">Aprovar <i class="fas fa-thumbs-up"></i></button>
                    </form>
                </li>
            @endif
        @endcan

</form>

    </ul>
</td>

{{--
@if ($site->status == "Habilitado")
<td>
<form method="POST" action="/sites/{{ $site->id }}/disable">
{{ csrf_field() }}
<button type="submit" class="btn btn-info">Desabilitar</button>
</form></td>

@elseif ($site->status == "Desabilitado")
<td><form method="POST" action="/sites/{{ $site->id }}/enable">
{{ csrf_field() }}
<button type="submit" class="btn btn-success">Habilitar</button>
</form>

<form method="POST" action="/sites/{{ $site->id }}/delete">
{{ csrf_field() }}
<button type="submit" class="btn btn-dark">Deletar</button>
</form></td>

@elseif($site->status != "Servidor Offline")
<td>
<form method="POST" action="/sites/{{ $site->id }}/clone">
{{ csrf_field() }}
<button type="submit" class="btn btn-primary">Recriar</button>
</form>

</td>
@endif
--}}

</tr>
