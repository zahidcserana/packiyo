<div class="form-group {{ $containerClass ?? '' }}">
    @if ($label)
        <label class="{{$labelClass ?? ''}}">{{ $label }}</label>
    @endif
    
    <input
        name="{{ $name }}"
        type="range"
        class="custom-range {{ $class ?? '' }}"
        min="{{ $min ?? 0 }}"
        max="{{ $max ?? 100 }}"
        step="{{ $step ?? 1 }}"
        value="{{ old($name, $value ?? $defaultValue) }}"
    >
</div>
