@props([
    'label' => $label,
    'name' => $name,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
])

<div class="form-group">
  <label for="exampleFormControlInput1">{{ $label }}</label>
  <input type=" {{ $type }}" 
  class="form-control" 
  name="{{ $name }}"
  id="exampleFormControlInput1" 
  placeholder=""
  value="{{ $value }}">
</div>
