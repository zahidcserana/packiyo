@props([
    'containerClass' => 'col-12',
    'isMultiple' => true,
    'url' => '',
    'formId' => '',
    'images' => null,
    'deleteImageUrl' => '',
])

<div class="form-group {{ $containerClass }}">
    <div class="row">
        <div
            id="dropzone-body"
            data-multiple="{{ $isMultiple }}"
            data-url="{{ $url }}"
            action="{{ $url }}"
            data-form="{{ $formId }}"
            data-images="{!! $images !!}"
            data-delete-image-url="{{ $deleteImageUrl }}"
            class="dropzone dropzone-multiple col-md-3 col-xs-12 mb-2"
        >
            <div class="dz-message" data-dz-message>
                <button type="button" class="upload-image-button d-flex">
                    <i class="picon-upload-light icon-lg icon-black mr-2"></i>
                    {{ __('Click or drop image') }}
                </button>
            </div>
            <div class="fallback">
                <input name="file" type="file"/>
            </div>
        </div>
        <div class="col-md-9 col-xs-12 mb-2">
            <div id="previews" class="h-100"></div>
        </div>
    </div>
</div>

@once
    @push('js')
        <script>
            const dzb = $(document).find('#dropzone-body');
            const isMultiple = dzb.attr("data-multiple");
            let images = dzb.attr("data-images");

            Dropzone.options.dropzoneBody = {
                autoProcessQueue: false,
                uploadMultiple: isMultiple,
                addRemoveLinks: true,
                dictRemoveFile: '<div class="deleteBtn"><i class="fas fa-trash-alt text-lightGrey"></i></div>',
                parallelUploads: isMultiple ? 10 : 1,
                acceptedFiles: ".jpeg,.jpg,.png,.gif",
                maxFiles: isMultiple ? 10 : 1,
                maxFilesize: 8192,
                previewsContainer: "#previews",
                url: dzb.attr("data-url"),

                maxfilesexceeded: function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
                },

                init: function () {
                    window.dropzoneInstance = this

                    // Handle file uploaded event
                    $(document).on('dropzoneFileUploaded', function (e, response) {
                        dropzoneInstance.removeAllFiles()

                        let images = []

                        // Right now only product has images
                        // Same check should be for all models which can have images in the future
                        if ('product' in response) {
                            images = response.product?.product_images
                        }

                        if (images.length) {
                            $('#previews').empty()

                            $.each(images, function(key, value) {
                                addImage(dropzoneInstance, value)
                            })
                        }
                    })

                    if (images) {
                        const parsedImages = JSON.parse(images);

                        if ( parsedImages.id ) {
                            addImage(dropzoneInstance, parsedImages);
                        } else {
                            for (i = 0; i < parsedImages.length; i++) {
                                addImage(dropzoneInstance, parsedImages[i]);
                            }
                        }
                    }
                },
                removedfile: function (file, response) {
                    if (file.id) {
                        $.get(dzb.attr('data-delete-image-url'), { id: file.id }, function(data) {
                            if (data.success) {
                                file.previewElement.remove();
                                $(document).find('img[src="' + file.source + '"].detailsImage').remove()
                            } else {
                                toastr.error('Error, try to restart page.');
                            }
                        })
                    } else {
                        file.previewElement.remove();
                    }
                }
            }

            function addImage (dropzoneInstance, parsedImage) {
                dropzoneInstance.emit("addedfile", parsedImage)
                dropzoneInstance.emit("thumbnail", parsedImage, parsedImage.source)
                dropzoneInstance.emit("complete", parsedImage)
            }
        </script>
    @endpush
@endonce
