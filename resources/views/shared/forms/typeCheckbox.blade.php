@php
    $inputFirst = $inputFirst ?? false;
@endphp
@php($labelHTML = '<label for="chk-'.$name.'" class="w-100 text-neutral-text-gray font-weight-600 font-xs" data-id="'.$name.'">'.$label.'</label>')
<div class="{{ $containerClass ?? '' }}">
    @if(!$inputFirst)
        <div class="{{ $labelClass ?? '' }}">
            {!! $labelHTML !!}
        </div>
    @endif
    <div class="custom-form-checkbox {{ $inputClass ?? '' }}">
        <input type="hidden" name="{{$name}}" value="0" />
        <input class="{{ $inputTypeClass ?? '' }}" name="{{$name}}" id="chk-{{$name}}" type="checkbox" {{$checked ?? false === true ? 'checked' : ''}} value="{{$value?? 1 }}">
        <label class="text-black font-weight-600" for="chk-{{$name}}"></label>
    </div>
    @if($inputFirst)
        <div class="{{ $labelClass ?? '' }}">
            {!! $labelHTML !!}
        </div>
    @endif
</div>

