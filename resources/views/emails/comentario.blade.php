Novo comentário no chamado {{$comentario->chamado->site->id}}/{{$comentario->chamado->id}} 
para o site {{ $comentario->chamado->site->dominio.config('sites.dnszone') }}

<div>
Número USP: {{ $user->codpes }} <br>
Nome: {{ $user->name }} 
Número do chamado: {{$comentario->chamado->site->id}}/{{$comentario->chamado->id}}
Chamado: <a href="{{config('app.url')}}/{{$comentario->chamado->site->id}}/{{$comentario->chamado->id}}">
    {{config('app.url')}}/{{$comentario->chamado->site->id}}/{{$comentario->chamado->id}}
</a>

<br>

<b>Status:</b> {{ $comentario->chamado->status }}
<div>
<b>Comentário:</b> {!! $comentario->comentario !!}
</div>

Mensagem automática do sistema de gestão de sites: {{ config('app.url') }}

