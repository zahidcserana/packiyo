window.ImageDropzoneAjax = function (event) {
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
            const myDropzone = this;
            $(document).on('click', "#" + dzb.attr("data-button"), function (e) {
                e.preventDefault();
                e.stopPropagation();

                $(document).find('.form-error-messages').remove()
                let _form = $(this).closest('.productForm');
                let form = _form[0];
                let formData = new FormData(form);
                _form.find('.loading').removeClass('d-none')

                const data = $('#' + dzb.attr("data-form")).serializeArray();

                $.each(data, function (key, el) {
                    formData.append(el.name, el.value);
                });

                window.updateCkEditorElements()

                if (myDropzone.getQueuedFiles().length > 0) {
                    $.each(myDropzone.getQueuedFiles(), function (key, el) {
                        formData.append('file[]', el);
                    });
                }

                $.ajax({
                    type: 'POST',
                    url: _form.attr('action'),
                    enctype: 'multipart/form-data',
                    headers: {'X-CSRF-TOKEN': formData.get('_token')},
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        _form.find('.loading').addClass('d-none')
                        _form.find('.saveSuccess').removeClass('d-none').css('display', 'block').fadeOut(5000)
                        _form.find('.notes-data span').html(data.product.notes)
                        _form.removeClass('editable')

                        myDropzone.removeAllFiles();
                        const container = $('#rows_container');
                        let modal = $('#productCreateModal');
                        let detailsImgCont = $('#detailsImageContainer');
                        detailsImgCont.append('<p class="text-center w-100">No images</p>')
                        if (data.product.product_images.length) {
                            $('#previews').empty()
                            detailsImgCont.empty()
                            $.each(data.product.product_images, function(key,value) {
                                let mockFile = { name: value.name, size: value.size };
                                $('#detailsImageContainer').append('<img class="detailsImage mr-2" src="' + value.source + '">')
                                myDropzone.emit("addedfile", mockFile);
                                myDropzone.emit("thumbnail", mockFile, value.source);
                                myDropzone.emit("complete", mockFile);
                            });
                        }

                        modal.modal('toggle');
                        modal.find('form').trigger("reset");
                        container.find('select').prop('disabled', 'disabled');
                        container.find('input').prop('disabled', 'disabled');
                        container.addClass('d-none');
                        modal.find('.nav-link.text-danger').removeClass('text-danger');
                        window.location.hash = '';
                        $('#supplier_container').find('tr').remove();

                        reloadAuditLog()
                        toastr.success(data.message)

                        // TODO Refactor later to refresh only updated instance instead of all
                        $('table[id$=-table]').each(function () {
                            window.dtInstances['#' + $(this).attr('id')].ajax.reload()
                        })

                        let form = $('#product-create-form')
                        form.trigger('reset')
                        form.find('.ajax-user-input').val(null).trigger('change')
                        form.find('select').trigger('change')
                        $('#product-create-form #previews .dz-preview').remove()
                    },
                    error: function (messages) {
                        let parent = $('#globalForm');

                        if (_form.data('type') === 'POST') {
                            parent = $('#productCreateModal');
                        }

                        _form.find('.loading').addClass('d-none')
                        _form.find('.saveError').removeClass('d-none').removeClass('d-none').css('display', 'block').fadeOut(5000)
                        _form.addClass('editable')

                        $.map(messages.responseJSON.errors, function (value, key) {
                            let label = parent.find('label[data-id="' + key + '"]')
                            label.append('<span class="validate-error text-danger form-error-messages">&nbsp;&nbsp;&nbsp;&nbsp;' + value[0] + '</span>')

                            let error_type = key.split('.')

                            if (error_type && error_type.length && error_type[0] === 'kit_items') {
                                $(document).find('.validation_errors').append('<span class="validate-error text-danger form-error-messages">' + value[0] + '</span><br>')
                            }

                            let hasError = label.closest('.tab-pane').attr('id');
                            $(document).find('a[href="#' + hasError + '"]').addClass('text-danger')

                            if (Array.isArray(value)) {
                                $.each(value, function (k, v) {
                                    toastr.error(v)
                                })
                            } else {
                                toastr.error(value)
                            }

                        })

                        let hasErrorTab = $(document).find('.validate-error').closest('.tab-pane').attr('id');

                        $(document).find('a[href="#' + hasErrorTab + '"]').first().trigger('click')
                    }
                });
            });
            $(document).on('click', '.globalSave', function (e) {
                e.preventDefault();
                e.stopPropagation();

                $(document).find('.form-error-messages').remove()
                let _form = $(this).closest('#globalForm');

                let formData = new FormData();

                const forms = _form.find('form');

                window.updateCkEditorElements()

                $.each(forms, function (index, form) {
                    let data = $(form).serializeArray()
                    $.each(data, function (key, el) {
                        formData.append(el.name, el.value);
                    })
                })


                if (myDropzone.getQueuedFiles().length > 0) {
                    $.each(myDropzone.getQueuedFiles(), function (key, el) {
                        formData.append('file[]', el);
                    });
                }
                $('.product-details-checkboxes-title').removeClass('d-none');
                $('.priority-counting-checkbox').addClass('d-none');
                $('.serial-number-checkbox').addClass('d-none');

                $.ajax({
                    type: 'POST',
                    url: _form.data('form-action'),
                    enctype: 'multipart/form-data',
                    headers: {'X-CSRF-TOKEN': formData.get('_token')},
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        myDropzone.removeAllFiles();
                        $('.smallForm').removeClass('editable');
                        let detailsImgCont = $('#detailsImageContainer');
                        detailsImgCont.append('<p class="text-center w-100">No images</p>')
                        _form.find('.notes-data span').html(data.product.notes)
                        _form.find('#edit-kit-items').addClass('d-none')
                        _form.find('#product-kits-table-container').removeClass('d-none')
                        if (data.product['kit_type'] == 0) {
                            $('#kits-form').addClass('d-none');
                            $('#locations-form').removeClass('d-none')
                            $('#kit-items-table tr').slice(1).remove()
                        } else if (data.product['kit_type'] == 1) {
                            $('#kits-form').removeClass('d-none')
                            $('#locations-form').addClass('d-none')
                        }
                        window.dtInstances['#product-kits-table'].ajax.reload()
                        $.each($('#kit-items-table tr'), function (index, value) {
                            if ($(value).find('select').val() == null || $(value).find("td:eq(1) input").val() === '') {
                                $(value).remove();
                            }
                        })
                        if (data.product.product_images.length) {
                            $('#previews').empty()
                            detailsImgCont.empty()
                            $.each(data.product.product_images, function(key,value) {
                                let mockFile = { name: value.name, size: value.size };
                                $('#detailsImageContainer').append('<img class="detailsImage mr-2" src="' + value.source + '">')
                                myDropzone.emit("addedfile", mockFile);
                                myDropzone.emit("thumbnail", mockFile, value.source);
                                myDropzone.emit("complete", mockFile);
                            });
                        }

                        reloadAuditLog()
                        toastr.success(data.message)
                        new updateProductLocations(data.product.id)
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    },
                    error: function (messages) {
                        if (messages.responseJSON.errors) {
                            $.each(messages.responseJSON.errors, function (key, value) {
                                toastr.error(value)
                            });
                        }
                        $.map(messages.responseJSON.errors, function (value, key) {
                            let label = _form.find('label[data-id="' + key + '"]')
                            label.append('<span class="validate-error text-danger form-error-messages">&nbsp;&nbsp;&nbsp;&nbsp;' + value[0] + '</span>')

                            let error_type = key.split('.')

                            if (error_type && error_type.length && error_type[0] === 'kit_items') {
                                $(document).find('.validation_errors').append('<span class="validate-error text-danger form-error-messages">' + value[0] + '</span><br>')
                            }

                            if (Array.isArray(value)) {
                                $.each(value, function (k, v) {
                                    toastr.error(v)
                                })
                            } else {
                                toastr.error(value)
                            }
                        })
                        $(document).find('.validate-error').eq(0).closest('form').addClass('editable')

                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    }
                });
            })

            this.on('sending', function (file, xhr, formData) {
                const data = $('#' + dzb.attr("data-form")).serializeArray();
                $.each(data, function (key, el) {
                    formData.append(el.name, el.value);
                });
            });

            if (images) {
                const parsedImages = JSON.parse(images);

                if ( parsedImages.id ) {
                    addImage(myDropzone, parsedImages);
                } else {
                    for (i = 0; i < parsedImages.length; i++) {
                        addImage(myDropzone, parsedImages[i]);
                    }
                }
            }
        },
        success: function (file, response) {
            if (dzb.attr("data-redirect")) {
                window.location.replace(dzb.attr("data-redirect"));
            }
        },
        removedfile: function (file, response) {
            if (file.id) {
                $.get('/product/delete_product_image/', { id: file.id }, function(data, status){
                    if(data.success){
                        file.previewElement.remove();
                        $(document).find('img[src="' + file.source + '"].detailsImage').remove()
                    } else {
                        alert('Error, try to restart page.');
                    }
                });
            } else {
                file.previewElement.remove();
            }
        }
    }

    function addImage (myDropzone, parsedImage) {
        myDropzone.emit("addedfile", parsedImage);
        myDropzone.emit("thumbnail", parsedImage, parsedImage.source);
        myDropzone.emit("complete", parsedImage);
    }
};
