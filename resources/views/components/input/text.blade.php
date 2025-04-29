@props([
    'label' => '',
    'name' => $name,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'id' => 'text' . $name,
    'help' => '',
    'class' => '',
    'required' => '',
    'datalist' => [],
])

<div class="form-group">
  @if ($label)
    <label for="{{ $id }}">{{ $label }}</label>
  @endif

  <input type="{{ $type }}" class="form-control {{ $class }}" name="{{ $name }}"
    id="{{ $id }}" value="{{ $value }}" placeholder="{{ $placeholder }}" {{ $attributes }}
    @if ($datalist) list="{{ $name }}" autocomplete="off" @endif
    @if ($required) required @endif />

  @if ($help)
    <small class="form-text text-muted">{{ $help }}</small>
  @endif
  
  @if ($datalist)
  <datalist id="{{ $name }}">
    @foreach($datalist as $data)
      <option value="{{ $data }}"></option>
    @endforeach
  </datalist>
  @endif
</div>
