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
])

<div class="form-group">
  @if ($label)
    <label for="{{ $id }}">{{ $label }}</label>
  @endif

  <input type="{{ $type }}" class="form-control {{ $class }}" name="{{ $name }}"
    id="{{ $id }}" value="{{ $value }}" placeholder="{{ $placeholder }}"
    @if ($required) required @endif />

  @if ($help)
    <small class="form-text text-muted">{{ $help }}</small>
  @endif
</div>
