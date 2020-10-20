<h1>Administrador de conteúdo removido do site</h1>

<div>
O site {{ $site->dominio.config('sites.dnszone') }} teve um administrador de contéudo removido. 
<br>
Confira nossos tutoriais em: http://sti.fflch.usp.br/drupal
</div>

<br>
<div>
<b>Responsável pelo site:</b> {{ $nusp }} - {{ $name }} 
</div>

<br>
<div>
<b>Administrador de Conteúdo removido:</b> {{ $nusp_deleta_admin }} - {{ $name_deleta_admin }} 
</div>

<br>
Mensagem automática do sistema de gestão de sites: {{ config('app.url') }}

