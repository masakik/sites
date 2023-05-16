@if ($wp->errorMsg)
  <div class="alert alert-warning">
    <b>Erros no remoto</b><br>
    <div>Os erros apresentados podem prejudicar a coleta das demais informações do servidor WP</div>
    <pre>{{ $wp->errorMsg }}</pre>
  </div>
@endif

@if ($wp->isWp())
  @include('sites.ajax.wp-info')
@else
  <div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle text-warning"></i>
    Wordpress não está instalado ou possui erros<br>
    {{ $wp->error }}
  </div>
@endif