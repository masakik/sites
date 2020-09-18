<h1>Site aprovado</h1>

<div>
O site {{ $site->dominio.config('sites.dnszone') }} foi aprovado e está
na fila de processamento. Dentro de alguns instantes estará disponível para uso. 
<br>
Confira nossos tutoriais em: http://sti.fflch.usp.br/drupal
</div>

<br>
<div>
<b>Solicitante:</b> <br>
Nome: {{ $name }} 
</div>

<br>

<div>
<b>Justificativa:</b> {!! $site->justificativa !!}
</div>

Mensagem automática do sistema de gestão de sites: {{ config('app.url') }}

