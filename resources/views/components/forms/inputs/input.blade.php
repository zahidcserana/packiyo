@props([
    'type' => 'text',
    'name' => '',
    'value' => '',
    'label' => '',
    'labelWithCurrency' => false,
    'readOnly' => false,
    'containerClass' => 'col-lg-3 col-md-6 col-xs-12',
    'labelClass' => '',
    'inputClass' => '',
])

<div class="form-group {{ $containerClass }} {{ $type === 'hidden' ? 'd-none' : '' }}">
    @if ($label)
        <label for="{{ $name }}" class="{{ $labelClass }}">
            {{ $label }}
            @if ($labelWithCurrency && ! empty($sessionCustomer->currency))
                ({{ $sessionCustomer->currency }})
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        class="form-control font-weight-600 text-black {{ $inputClass }}"
        id="{{ $name }}"
        name="{{ $name }}"
        placeholder="{{ $label }}"
        {{ $readOnly ? 'readonly' : '' }}
        value="{{
            old(
                dot($name),
                ($type === 'date' && ! empty($value))
                    ? $value->format('Y-m-d')
                    : ($value ?? '')
            )
        }}"
    >
</div>
