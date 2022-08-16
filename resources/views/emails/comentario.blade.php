Novo comentário no chamado {{ $comentario->chamado->site->id }}/{{ $comentario->chamado->id }}
para o site {{ $comentario->chamado->site->dominio . config('sites.dnszone') }}

<div>
  Número USP: {{ $user->codpes }} <br>
  Nome: {{ $user->name }} <br>
  Número do chamado: {{ $comentario->chamado->site->id }}/{{ $comentario->chamado->id }} <br>
  Chamado: <a href="{{ route('chamados.show', $comentario->chamado) }}">
    {{ route('chamados.show', $comentario->chamado) }}
  </a>
</div>

<br>

<b>Status:</b> {{ $comentario->chamado->status }} <br>
<b>Comentário:</b> {!! $comentario->comentario !!} <br>

Mensagem automática do sistema de gestão de sites: {{ config('app.url') }}
