@if ($loginData)
  @if ($loginData['type'] === 'wordpress')
    <form method="post" target="_blank" action="sites/{{ $site->id }}/login">
      @csrf
      @if ($loginData['available'])
        <button class="btn btn-sm btn-outline-success" title="Login remoto">
          <i class="fas fa-tools"></i>
        </button>
      @else
        <button class="btn btn-sm btn-outline-success p-0" title="Login remoto pode não estar disponível!">
          <span class="fa-stack fa-1x">
            <i class="fas fa-tools fa-stack-1x"></i>
            <i class="fas fa-ban fa-2x fa-stack-1x text-danger"></i>
          </span>
        </button>
      @endif
    </form>
  @endif

  @if ($loginData['type'] === 'drupal')
    <a href="{{ $loginData['url'] }}" class="btn btn-sm btn-outline-success"
      target="_blank" title="Logon">
      <i class="fas fa-sign-in-alt"></i>
    </a>
  @endif
@endif
