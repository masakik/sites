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

{{-- aqui carrega os dados que serão exibidos no header do card wordpress --}}
<div class="d-none wp-info-header">
  &nbsp; @include('sites.partials.logon-btn')
  &nbsp; {{ $wp->info['date'] }}
</div>

{{-- aqui carrega os dados que serão exibidos no div do card do gerenciador --}}
<div class="d-none gerenciador-data">
  @foreach ($wp->gerenciador as $k => $v)
    {{ $k }}: <b>{{ $v }}</b><br>
  @endforeach
</div>

{{-- <div>
  <div class="mt-2"><b>Docs</b></div>
  <div><a href="https://endoflife.date/wordpress" target="_wp_endoflife">End of life</a></div>
  <div> Testado desde WP 5.6 até 6.2</div>
</div> --}}
