<div class="{{$containerClass??''}} form-group mb-0 mx-2 text-left mb-3 d-flex flex-column justify-content-start{{ $errors->has($name) ? ' has-danger' : '' }}">
    <label class="form-control-label text-neutral-text-gray font-weight-600 font-xs" for="input-{{ $name }}">{{ $label }}</label>
    <div class="colorPicker square input-group input-group-alternative input-group-merge w-100">
        <input type="text" name="{{ $name }}" class="coloris form-control font-weight-600 text-black h-auto p-2" value="{{ $value ?? '' }}">
    </div>
</div>
@push('js')
    <script type="text/javascript">
        Coloris({
            el: '.coloris',
            swatches: [
                '#F7860B',
                '#e9c46a',
                '#FF6A55',
                '#4dc0b5',
                '#83BF6E',
                '#5D5D5D'
            ]
        });
    </script>
@endpush
