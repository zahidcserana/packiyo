@props([
    'name' => '',
    'label' => '',
    'placeholder' => '',
    'containerClass' => 'col-lg-3 col-md-6 col-xs-12',
    'labelClass' => '',
    'inputClass' => '',
    'url' => '',
    'value' => [],
    'minInputLength' => 0,
    'readonly' => false,
])

<div class="form-group {{ $containerClass }}">
    @if ($label !== false)
        <label class="{{ $labelClass }}">{{ $label }}</label>
    @endif

    @if ($readonly)
        <input name="{{ $name }}" value="{{ $value['id'] }}" hidden>
        <input value="{{ $value['text'] }}" class="form-control" readonly>
    @else
        <select
            name="{{ $name }}"
            class="{{ $inputClass }}"
            data-ajax--url="{{ $url }}"
            data-placeholder="{{ $placeholder }}"
            data-minimum-input-length="{{ $minInputLength }}"
            data-toggle="select"
        >
            @if (! empty($value))
                <option
                    value="{{ $value['id'] }}"
                    selected="selected"
                >{{ $value['text'] }}</option>
            @endif
        </select>
    @endif
</div>
