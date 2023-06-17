<div class="form-group mb-0 {{ $containerClass ?? '' }}" readonly>
    @if (!empty($label))
        <label class="form-control-label text-neutral-text-gray font-weight-600 font-xs {{$labelClass ?? ''}} d-flex justify-content-end flex-column" for="input-{{ $name }}">{{ $label }}
            @if(!empty($error) && $error)
                <span class="text-danger form-error-messages font-weight-600 font-xs">&nbsp;&nbsp;&nbsp;{{ $error }}</span>
            @endif
        </label>
    @endif

    <select
        @if(isset($attributes))
            @foreach($attributes as $key => $val)
                {{$key}} = {{$val}}
            @endforeach
        @endif
        name="{{$name}}"
        id="input-{{ $name }}{{ $idPrefix ?? null }}"
        data-placeholder="{{ $placeholder ?? '' }}"
        @if (!empty($allowClear)) data-allow-clear=true @endif
        @if (!empty($form)) form="{{ $form }}" @endif
        @if (!empty($class)) class="{{ $class }}" @endif
        data-toggle="select"
        @if (!empty($dropdownParent)) data-dropdown-parent="{{ $dropdownParent }}" @endif
    >
        @foreach($options as $key => $val)
            <option value="{{$key}}" {{ $key == ($value ?? '') ? 'selected' : '' }}>{{ $val }}</option>
        @endforeach
    </select>
</div>
