window.ImageDropzone = function (event) {
    const dzb = $('#dropzone-body');
    const isMultiple = dzb.data('multiple');
    const images = dzb.data('images');
    let uploadedImages = []

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
            const myDropzone = this;

            myDropzone.on('addedfile', function (file) {
                uploadedImages.push(file)
            });

            if (images) {

                if (images.id) {
                    addImage(myDropzone, images);
                } else {
                    for (let i = 0; i < images.length; i++) {
                        addImage(myDropzone, images[i]);
                    }
                }
            }
        }
    }

    function addImage(myDropzone, parsedImage) {
        myDropzone.emit('addedfile', parsedImage);
        myDropzone.emit('thumbnail', parsedImage, parsedImage.source);
        myDropzone.emit('"complete', parsedImage);
    }

    $(document).on('click', "#" + dzb.data('button'), function (e) {
        e.preventDefault();

        window.updateCkEditorElements()

        const _form = $('#' + dzb.data('form'));
        const form = _form[0];
        let formData = new FormData(form);

        uploadedImages.forEach(function (image) {
            formData.append(dzb.data('name'), image)
        })

        $.post({
            enctype: 'multipart/form-data',
            headers: {'X-CSRF-TOKEN': formData.get('_token')},
            url: dzb.data('url'),
            data: formData,
            async: false,
            processData: false,
            contentType: false,
            success: function (data) {
                toastr.success(data.message)
            },
            error: function (messages) {
                $.map(messages.responseJSON.errors, function (message) {
                    if (Array.isArray(message)) {
                        $.each(message, function (k, v) {
                            toastr.error(v)
                        })
                    } else {
                        toastr.error(message)
                    }
                })
            }
        });
    })
};
