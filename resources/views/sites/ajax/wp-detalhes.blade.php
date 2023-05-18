@if ($wp->info['errorMsg'])
  <div class="alert alert-warning">
    <b>Erros no remoto</b><br>
    <div>Os erros apresentados podem prejudicar a coleta das demais informações do servidor WP</div>
    <pre>{{ $wp->info['errorMsg'] }}</pre>
  </div>
@endif

@if ($wp->isWp())
  @include('sites.ajax.wp-info')
@else
  <div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle text-warning"></i>
    Wordpress não está instalado ou possui erros<br>
    {{ $wp->info['error'] }}
  </div>
@endif

<div class="d-none wp-info-date">{{ $wp->info['date'] }}</div>

<div>
  <div class="mt-2"><b>Docs</b></div>
  <div><a href="https://endoflife.date/wordpress" target="_wp_endoflife">End of life</a></div>
  <div> Testado desde WP 5.6 até 6.2</div>
</div>