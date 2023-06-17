<div class="dropzone-container {{ $dropzoneContainerClass ?? '' }}">
    <div
        id="dropzone-body"
        data-multiple="{{ $isMultiple }}"
        data-redirect="{{ $redirect ?? null }}"
        data-url="{{ $url }}"
        action="{{ $url }}"
        data-form="{{ $formId }}"
        data-button="{{ $buttonId }}"
        data-images="{{ $images }}"
        data-name="{{ $name }}"
        class="dropzone dropzone-multiple p-0"
    >

        <div class="dz-message" data-dz-message>
            <button type="button" class="upload-image-button d-flex">
                <i class="picon-upload-light icon-lg icon-black mr-2"></i>
                {{ __('Click or drop image') }}
            </button>
        </div>
        <div class='fallback'>
            <input name='{{ $name ?? 'file' }}' type='file'/>
        </div>
    </div>

    <div id="previews">

    </div>
</div>
@push('js')
    <script>
        new ImageDropzoneAjax();
    </script>
@endpush
