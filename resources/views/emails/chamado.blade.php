Novo chamado para o site {{ $chamado->site->dominio.config('sites.dnszone') }}

<div>
Número USP: {{ $user->codpes }} <br>
Nome: {{ $user->name }} 
Número do chamado: {{$chamado->site->id}}/{{$chamado->id}}
Chamado: <a href="{{config('app.url')}}/{{$chamado->site->id}}/{{$chamado->id}}">
  {{config('app.url')}}/{{$chamado->site->id}}/{{$chamado->id}}
</a>
</div>

<br>

<b>Tipo:</b> {{ $chamado->tipo }}
<div>
<b>Chamado:</b> {!! $chamado->descricao !!}
</div>

Mensagem automática do sistema de gestão de sites: {{ config('app.url') }}

