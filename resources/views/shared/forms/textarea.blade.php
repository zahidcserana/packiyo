<div class="form-group {{ $errors->has($name) ? ' has-danger' : '' }} {{ $containerClass ?? 'mb-3' }}">
    <label class="form-control-label text-neutral-text-gray font-weight-600 font-xs" for="input-{{ $name }}">{{ $label }}</label>
    <div class="input-group input-group-alternative input-group-merge">
        <textarea class="{{$class??''}} form-control font-weight-600 text-black h-auto p-2{{ $errors->has($name) ? ' is-invalid' : '' }}" name="{{ $name }}"
              id="input-{{ $name }}" rows="{{ $rows ?? 3 }}" placeholder="{{ $label }}">{!! $value ?? '' !!}</textarea>
    </div>
</div>
