@php($labelHtml = $label != '' ? '<label for="" class="text-neutral-text-gray font-weight-600 font-xs" data-id="'.$name.'">'.$label.'</label>' : '')
<div class="{{ $containerClass ?? '' }} editSelect">
    {!! $labelHtml !!}
    <select
        @if(isset($attributes))
            @foreach($attributes as $key => $val)
                {{$key}} = {{$val}}
            @endforeach
        @endif
        name="{{ $name }}"
        data-toggle="select"
        data-placeholder="{{ $placeholder ?? '' }}"
        class="{{ $className ?? '' }}"
        @if (!empty($dropdownParent)) data-dropdown-parent="{{ $dropdownParent }}" @endif
    >
        @foreach($options as $key => $val)
               <option value="{{$key}}" {{ $key == $value ? 'selected' : '' }}>{{ $val }}</option>
        @endforeach
    </select>
</div>
