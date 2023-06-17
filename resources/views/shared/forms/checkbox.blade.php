<div class="{{ $containerClass ?? '' }} custom-form-checkbox">
    <input type="hidden" name="{{$name}}" value="0"/>
    <input class="" name="{{$name}}" id="chk-{{$name}}" type="checkbox" {{$checked ?? false === true ? 'checked' : ''}} value="{{$value?? 1 }}">
    <label class="text-black font-weight-600" for="chk-{{$name}}">{{$label}}</label>
</div>
