@props([
    'name' => '',
    'value' => 1,
    'checked' => false,
    'label' => '',
    'containerClass' => 'col-lg-3 col-md-6 col-xs-12',
    'labelClass' => '',
])

<div class="form-group {{ $containerClass }}">
    <div class="custom-form-checkbox">
        <label>&nbsp;</label>
        
        <input type="hidden" name="{{ $name }}" value="0" />
        
        <input
            name="{{ $name }}"
            id="checkbox-{{ $name }}"
            type="checkbox"
            {{ $checked ? 'checked' : '' }}
            value="{{ $value }}"
        />
        
        <label class="{{ $labelClass }}" for="checkbox-{{ $name }}">
            {{ $label }}
        </label>
    </div>
</div>