@props([
    'name' => '',
    'value' => '',
    'label' => '',
    'ckeditor' => true,
    'readOnly' => false,
    'containerClass' => 'col-lg-3 col-md-6 col-xs-12',
    'labelClass' => '',
    'inputClass' => '',
])

<div class="form-group {{ $containerClass }}">
    @if ($label)
        <label for="{{ $name }}" class="{{ $labelClass }}">
            {{ $label }}
        </label>
    @endif

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $readOnly ? 'readonly' : '' }}
        class="form-control font-weight-600 text-black {{ $ckeditor ? 'editor' : '' }} {{ $inputClass }}"
    >{!!
        $value
    !!}</textarea>
</div>
