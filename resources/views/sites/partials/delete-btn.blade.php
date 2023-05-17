<form method="POST" action="sites/{{ $site->id }}" class="delete-form">
  @csrf
  @method('delete')
  <button type="submit" class="delete-item btn btn-danger">Deletar <i class="fas fa-trash-alt"></i></button>
</form>

@once
  @section('javascripts_bottom')
  @parent
    <script>
      $(document).ready(function() {
        $(".delete-form").submit(function() {
          if (!confirm("Tem certeza?")) {
            event.preventDefault();
          }
        });
      });
    </script>
  @endsection
@endonce
