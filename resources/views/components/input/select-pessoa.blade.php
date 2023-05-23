{{-- Copiado de senhaunica-socialite
  22/5/2023
  - inclusão da variavel name com default para codpes
  - inclusão do dropdownparent para o caso de modal
  --}}
@props([
    'prepend' => '',
    'append' => '',
    'label' => '',
    'groupClass' => '',
    'class' => '',
    'name' => 'codpes',
    'id' => 'select-' . mt_rand(1000000, 9999999),
])

<div class="senhaunica-select-pessoa form-group {{ $groupClass }}">
  @if ($label)<label for="{{ $id }}">{{ $label }}</label>@endif
  <div class="input-group">
    @if ($prepend)
      <div class="input-group-prepend">
        <div class="input-group-text">{!! $prepend !!}</div>
      </div>
    @endif
    <select id="{{ $id }}" name="{{ $name }}" class="form-control {{ $class }}" autocomplete="off"
      {{ $attributes }}>
      <option>Digite o nome ou número USP..</option>
    </select>

    {{ $slot }}

    @if ($append)
      <div class="input-group-append">
        <div class="input-group-text">{!! $append !!}</div>
      </div>
    @endif
  </div>

  @error('codpes') <span class="small text-danger">{{ $message }}</span> @enderror
</div>

{{-- @once --}}
  @section('javascripts_bottom')
    @parent
    <script>
      $(function() {

        let select = $('#{{ $id }}')

        // verificar se estamos dentro de modal ou não
        // todo: se tiver um dentro de modal e outro fora, o de fora não vai funcionar.
        let oDropdownParent = $('body')
        if (select.closest('.modal').length == 1) {
          oDropdownParent = select.closest('.modal')
        }

        select.select2({
          ajax: {
            url: '{{ route('SenhaunicaFindUsers') }}',
            dataType: 'json',
            delay: 1000
          },
          dropdownParent: oDropdownParent,
          minimumInputLength: 4,
          theme: 'bootstrap4',
          width: 'resolve',
          language: 'pt-BR'
        })

        // coloca o focus no select2
        // https://stackoverflow.com/questions/25882999/set-focus-to-search-text-field-when-we-click-on-select-2-drop-down
        $(document).on('select2:open', () => {
          document.querySelector('.select2-search__field').focus();
        });

        // abre automaticamente o select quando abre o modal
        // todo: talvez tenha de limitar o escopo
        select.closest('.modal').on('shown.bs.modal', function() {
          select.select2('open')
          console.log('modal abriu')
        })

      })
    </script>
  @endsection
{{-- @endonce --}}
