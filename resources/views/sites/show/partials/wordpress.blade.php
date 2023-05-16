<b>Wordpress</b>
<div>
  <div><b>Config</b>@include('sites.partials.config-btn')</div>
  <div>Host: {{ $site->config['host'] }}:{{ $site->config['port'] ?? '' }} </div>
  <div>Path: {{ $site->config['path'] }}</div>
  {{-- <div>Usuário dono: {{ $site->config['suUser'] ?? '' }}</div> --}}
</div>

<div id="wordpress-details">
  <div><b>WP Options</b></div>
  <div class="alert alert-info d-inline-flex">
    <div class="spinner-border spinner-border-sm mt-1" role="status"></div>
    <div class="ml-3">Carregando ...</div>
  </div>
</div>


<div>
  <div class="mt-2"><b>Docs</b></div>
  <div><a href="https://endoflife.date/wordpress" target="_wp_endoflife">End of life</a></div>
  <div> Testado desde WP 5.6 até 6.2</div>
</div>

@section('javascripts_bottom')
  <script>
    $(document).ready(function() {
      $.get("{{ route('sites.show', $site) }}?get=wp_detalhes", function(data){
        $('#wordpress-details').hide().html(data).slideDown()
      })
      .fail(function(){
        $('#wordpress-details').html('Algo saiu errado ...')

      })
    })

  </script>
@endsection
