<div class="form-group mb-0 text-left mb-3 {{ $containerClass ?? '' }}" readonly>
    @if($label)
        <label data-id="{{ $dataId ?? $name }}" class="form-control-label text-neutral-text-gray font-weight-600 font-xs {{$labelClass ?? ''}}">{{ $label }}</label>
    @endif
    @if(isset($readonly) ?? false)
        <div class="input-group input-group-alternative input-group-merge">
            <input name="{{ $name }}" value="{{ $default['id'] }}" hidden>
            <input value="{{ $default['text'] }}" class="form-control font-weight-600 text-neutral-gray h-auto p-2" readonly>
        </div>
    @else
        <select
            {{$attribute ?? ''}}
            name="{{$name}}"
            @if(isset($id))
                id="{{ $id }}"
            @endif
            class="{{$className}}"
            data-ajax--url="{{ $url }}"
            data-placeholder="{{ $placeholder }}"
            @if(!isset($searchOnClick))
                data-minimum-input-length="1"
            @endif
            @if (!empty($form)) form="{{ $form }}" @endif
            data-toggle="select"
            @if (!empty($fixRouteAfter)) data-fix-route-after="{{ $fixRouteAfter }}" @endif
        >
            @if (!empty($default) && $default['id'] && $default['text'])
                <option
                    value="{{ $default['id'] }}"
                    selected="selected"
                >
                    {{ $default['text'] }}
                </option>
            @endif
        </select>
    @endif
</div>
