<div class="card mt-3">
  <div class="card-header py-1 form-inline">
    <i class="fab fa-wordpress"></i> Wordpress <span id="wp-info-date" class="ml-3"></span>
    @include('sites.show.partials.wp-refresh')
  </div>
  <div class="card-body py-1">

    <div id="wordpress-details">
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
      $.get("{{ route('sites.show', $site) }}?get=wp_detalhes", function(data) {
          $('#wordpress-details').hide().html(data).slideDown()
          $('#wp-info-date').html($('#wordpress-details').find('.wp-info-date').html())
        })
        .fail(function() {
          $('#wordpress-details').html('Algo saiu errado ...')
        })
    })
  </script>
@endsection
