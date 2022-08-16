@extends('layouts.app')

@section('content')
  @parent
  <h3> Variáveis de ambiente</h3>
  <h4>Sites</h4>
  APP_NAME = {{ config('app.name') }}<br>
  APP_URL = {{ config('app.url') }}<br>
  DB_CONNECTION = {{ config('database.default') }}<br>
  MAIL_MAILER = {{ config('mail.driver') }}<br>

  <br>
  DNSZONE = {{ config('sites.dnszone') }}<br>
  Habilitar subdomínio = {{ config('sites.subdominio') ? 'SIM' : 'NÃO' }}<br>
  <div>
    Chamados = {{ config('sites.chamados') }}<br>
    <small>
      Qual subsistema de chamados vai usar. Se "local", usa o sistema interno.
      Se "none" não usa subssistema de chamados.
      A implementar o uso do sistema uspdev/chamados.<br>
    </small>
  </div>
  <div>
    SITE_MANAGER = {{ config('sites.siteManager') }}<br>
    <small class="ml-2">
      Indica o gerenciador de site a ser utilizado.
      Por enquanto tem "aegir" e "local". Se site_manager = local, não vai ter config de administrador.
      Pretende-se implementar wordpress.<br>
    </small>
  </div>
  TUTORIAIS_URL = {{ config('sites.tutoriaisUrl') }}<br>

  {{-- @if (config('services.senhaunica.client_id'))
    <h4>Senha única</h4>
    Ambiente dev: {{ config('senhaunica.dev') }}<br>
    Key: {{ config('services.senhaunica.client_id') }}<br>
    Callback id: {{ config('senhaunica.callback_id') }}<br>
  @endif --}}
@endsection
