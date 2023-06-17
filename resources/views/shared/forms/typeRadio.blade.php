@php
    $labelClass = $labelClass ?? '';
@endphp
@php($labelHTML = '<label for="'.$inputID.'" class="'.$labelClass.' form-check-label" data-id="'.$name.'">'.$label.'</label>')
<div class="form-check {{ $containerClass ?? '' }}">
    <input class="{{ $inputTypeClass ?? '' }} form-check-input"
        type="radio"
        name="{{ $name }}"
        id="{{ $inputID }}"
        {{ $checked ?? false === true ? 'checked' : '' }}
        {{ $disabled ?? false === true ? 'disabled' : '' }}
        value="{{ $value ?? 1 }}">
    {!! $labelHTML !!}
</div>
