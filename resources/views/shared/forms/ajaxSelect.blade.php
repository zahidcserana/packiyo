<div class="form-group {{ $containerClass ?? 'mx-2' }}" readonly>
    <label class="form-control-label text-neutral-text-gray font-weight-600 font-xs {{$labelClass ?? ''}}">{{ $label }}</label>
    @if(isset($name) && ! empty($errors->first($name)))
        <span class="text-danger form-error-messages font-weight-600 font-xs">&nbsp;&nbsp;&nbsp;{{ $errors->first($name) }}</span>
    @endif
    @if(isset($readonly) ?? false)
        <input name="{{ $name }}" value="{{ $default['id'] }}" hidden>
        <input value="{{ $default['text'] }}" class="form-control w-100" readonly>
        @else
        <select
            {{$attribute ?? ''}}
            name="{{$name ?? ''}}"
            @if (! empty($className)) class="{{ $className }}" @endif
            data-ajax--url="{{ $url }}"
            data-placeholder="{{ $placeholder }}"
            data-minimum-input-length="{{ $minInputLength ?? '1' }}"
            data-name="{{ $dataName ?? '' }}"
            @if (!empty($form)) form="{{ $form }}" @endif
            @if (!empty($id)) id="{{ $id }}" @endif
            data-toggle="select"
            @if (!empty($fixRouteAfter)) data-fix-route-after="{{ $fixRouteAfter }}" @endif
            @if (!empty($dropdownParent)) data-dropdown-parent="{{ $dropdownParent }}" @endif
            @if (!empty($allowClear)) data-allow-clear=true @endif
        >
            @if (!empty($default))
                <option
                    value="{{ $default['id'] }}"
                    selected="selected"
                >{{ $default['text'] }}</option>
            @endif
        </select>
        @endif
</div>
