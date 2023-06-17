@props([
    'name' => '',
    'containerClass' => 'col-lg-3 col-md-6 col-xs-12',
    'labelClass' => '',
    'inputClass' => '',
    'options' => [],
    'value' => '',
])

<div class="form-group {{ $containerClass }}">
    <label for="{{ $name }}" class="{{ $labelClass }}">
        {{ __('Product type') }}
    </label>
    
    <select
        class="{{ $inputClass }}"
        name="{{ $name }}"
        id="{{ $name }}"
    >
        @foreach ($options as $optionValue => $optionName)
            <option
                value="{{ $optionValue }}"
                {{ $value === $optionValue ? 'selected' : '' }}
            >
                {{ $optionName }}
            </option>
        @endforeach
    </select>
</div>