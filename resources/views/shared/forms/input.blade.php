@if(!isset($type) || $type != 'hidden')
<div class="form-group {{ $containerClass ?? '' }}">
    <label class="form-control-label text-neutral-text-gray font-weight-600 font-xs {{$labelClass ?? ''}}" data-id="{{ $dataId ?? $name }}" for="input-{{ $name }}">{{ $label }}
        @if(!empty($error))
            <span class="text-danger form-error-messages">&nbsp;&nbsp;&nbsp;{{ $error }}</span>
        @endif
    </label>
    <input type="{{ $type ?? 'text' }}" name="{{ $name }}" id="input-{{ $name }}" class="{{$class??'p-2'}} form-control font-sm h-auto {{ $errors->has($name) ? ' is-invalid' : '' }}" placeholder="{{ $label }}" value="{{ old(dot($name), ((! empty($type)) && $type === 'date' && (! empty($value))) ? $value->format('Y-m-d') : ($value ?? '')) }}"  {{ $readOnly ?? ''}} {{ $required ?? '' }} @isset($min)min="{{$min}}"@endisset @isset($max)max="{{$max}}"@endisset>
</div>
@else
    <input type="{{ $type }}" name="{{ $name }}" id="input-{{ $name }}" value="{{ old(dot($name), ((! empty($type)) && $type === 'date' && (! empty($value))) ? $value->format('Y-m-d') : ($value ?? '')) }}">
@endif
