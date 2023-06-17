@php($labelHTML = '<label for="" class="text-neutral-text-gray font-weight-600 font-xs '.(isset($noCenter)? 'pr-2' : '' ).'" data-id="'.$name.'">'.$label.'</label>')
<div class="d-flex @if(!isset($noCenter)) justify-content-between @endif @if(!isset($noBorder)) border-bottom @endif py-3 align-items-center editCheckbox ">
    @if(!isset($checkboxFirst)) {!! $labelHTML !!} @endif
    <div class="custom-form-checkbox">
        <input type="hidden" name="{{$name}}" value="0" />
        <input class="" name="{{$name}}" id="chk-{{$name}}" type="checkbox" {{$checked ?? false === true ? 'checked' : ''}} value="{{$value?? 1 }}">
        <label class="text-black font-weight-600" for="chk-{{$name}}"></label>
    </div>
    @if(isset($checkboxFirst)) {!! $labelHTML !!} @endif
    @if(!isset($noYes))
    <span class="checkbox-result text-black font-weight-600">{{ $checked ?? false === true ? __('Yes') : '' }}</span>
    @endif
</div>
