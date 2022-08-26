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
  <div class="mt-3">
    Chamados = {{ config('sites.chamados') }}<br>
    <small>
      Qual subsistema de chamados vai usar. Se "local", usa o sistema interno.
      Se "none" não usa subssistema de chamados.
      A implementar o uso do sistema uspdev/chamados.<br>
    </small>
  </div>
  <div class="mt-3">
    SITE_MANAGER = {{ config('sites.siteManager') }}<br>
    <small class="ml-2">
      Indica o gerenciador de site a ser utilizado.
      Por enquanto tem "aegir" e "local". Se site_manager = local, não vai ter config de administrador.
      Pretende-se implementar wordpress.<br>
    </small>
  </div>
  <div class="mt-3">
    TUTORIAIS_URL = {{ config('sites.tutoriaisUrl') }}<br>
  </div>

  @if (config('services.senhaunica.client_id'))
    <div class="mt-3">
      <h4>
        <span class="badge badge-secondary">LIB</span>
        Senha única
        <a target="_senhaunica" href="https://github.com/uspdev/senhaunica-socialite" title="Github ..">
          <i class="fab fa-github"></i>
        </a>
      </h4>
      <div class="ml-2">
        @if (config('services.senhaunica.client_secret') == 'sua_super_chave_segura')
          <span class="text-danger">Senhaunica não configurada</span>
        @else
          Ambiente dev: {{ config('senhaunica.dev') }}<br>
          Key: {{ Illuminate\Support\Str::mask(config('services.senhaunica.client_id'), '*', 3) }}<br>
          Callback id: {{ config('senhaunica.callback_id') }}<br>
        @endif
      </div>
    </div>
  @endif
@endsection
