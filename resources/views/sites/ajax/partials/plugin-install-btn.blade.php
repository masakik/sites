<button class="btn btn-sm btn-link plugin-install-btn"><i class="fas fa-plus"></i></button>
<div class="d-none plugin-install-form ml-2">
  <form class="form-inline" method="POST" action="sites/{{ $site->id }}/wp-plugin">
    @csrf
    <input type="hidden" name="acao" value="install">
    <x-input.text name="plugin_name" class="btn-sm py-0 my-0" required="required"
      placeholder="Nome do plugin ou zip.." />
    <button type="submit" class="btn btn-sm btn-link">ok</button>
    <button type="cancel" class="btn btn-sm btn-link plugin-cancel-btn">
      <i class="fas fa-times text-danger"></i>
    </button>
  </form>
</div>

<script>
  $(document).ready(function() {
    // mostrando form
    $('.plugin-install-btn').on('click', function() {
      $(this).hide()
      $('.plugin-install-form').removeClass('d-none')
      $('.plugin-install-form').find('input').focus()
    })

    // escondendo form
    $('.plugin-cancel-btn').on('click', function() {
      $('.plugin-install-form').addClass('d-none')
      $('.plugin-install-btn').show()
    })
  })
</script>
