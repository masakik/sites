@props([
    'label' => $label,
    'name' => $name,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'id' => 'text' . $label . $name,
    'help' => '',
])

<div class="form-group">
  <label for="{{ $id }}">{{ $label }}</label>

  <input type=" {{ $type }}" class="form-control" name="{{ $name }}" id="{{ $id }}"
    value="{{ $value }}" />

  @if ($help)
    <small class="form-text text-muted">{{ $help }}</small>
  @endif
</div>
