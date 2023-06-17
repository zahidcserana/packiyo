<div class="form-group mb-0 mx-2 text-left mb-3" readonly>
    <label class="form-control-label text-neutral-text-gray font-weight-600 font-xs">{!! $label !!}</label>
    <div class="input-group input-group-alternative input-group-merge">
        <select
            @if(isset($attributes))
                @foreach($attributes as $key => $val)
                    {{$key}} = {{$val}}
                @endforeach
            @endif
            name="{{$name}}"
            data-placeholder="{{ $placeholder ?? '' }}"
            data-toggle="select"
            class="form-control font-weight-600 text-neutral-gray h-auto p-2 {{ $className ?? '' }}"
        >
            @foreach($options as $key => $val)
                <option value="{{$key}}" {{ $key == $value ? 'selected' : '' }}>
                    {{ $val }}
                </option>
            @endforeach
        </select>
    </div>
</div>
