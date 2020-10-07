<h1>Troca de responsável do site</h1>

<div>
O site {{ $site->dominio.config('sites.dnszone') }} teve  o reponsável alterado. 
<br>
Confira nossos tutoriais em: http://sti.fflch.usp.br/drupal
</div>

<br>
<div>
<b>Novo Responsável:</b> {{ $nusp_novo_responsavel }} - {{ $name_novo_responsavel }} 
</div>
<br>
<div>
<b>Responsável Anterior:</b>  {{ $nusp }} - {{ $name }}
</div>


<br>
Mensagem automática do sistema de gestão de sites: {{ config('app.url') }}

