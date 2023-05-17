<div class="card mt-3">
  <div class="card-header py-1">
    <i class="fas fa-code"></i>
    Html
  </div>
  <div class="card-body py-1">

    <div id="html-details">
      <div class="alert alert-info d-inline-flex">
        <div class="spinner-border spinner-border-sm mt-1" role="status"></div>
        <div class="ml-3">Carregando ...</div>
      </div>
    </div>

  </div>
</div>

@section('javascripts_bottom')
@parent
  <script>
    $(document).ready(function() {
      $.get("{{ route('sites.show', $site) }}?get=html_detalhes", function(data) {
          $('#html-details').hide().html(data).slideDown()
        })
        .fail(function() {
          $('#wordpress-details').html('Algo saiu errado ...')
        })
    })
  </script>
@endsection
