@if (config('app.env') == 'production')
  @php($port = '')
@else
  @php($port = ':8088')
@endif
@if ($site->status != 'Solicitado' && $site->status != 'Aprovado - Em Processamento')
  <a href="http://{{ $site->dominio }}{{ config('sites.dnszone') }}{{ $port }}/loginbytoken/?temp_token={{ $hashlogin }}&codpes={{ Auth::user()->codpes }}"
    class="btn btn-outline-success" target="_blank" title="Logon">
    <i class="fas fa-sign-in-alt"></i>
  </a>
@endif
