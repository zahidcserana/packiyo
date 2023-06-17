@props([
    'action' => '/',
    'successRedirect' => '/',
    'method' => '',
    'class' => '',
    'dataid' => '',
    'editable' => false,
    'formID' => \Illuminate\Support\Str::random(10),
])

<div class="base-ajax {{ $editable ? 'editable' : 'non-editable' }}">
    <form
        id="{{ $formID }}"
        class="px-3 {{ $class }}"
        action="{{ $action }}"
        method="{{ $method }}"
        enctype="multipart/form-data"
        data-success-redirect="{{ $successRedirect }}"
    >
        @csrf
        @method($method)

        <input type="hidden" name="order_id" value="{{ $dataid }}">

        <div class="row">
            {{ $slot }}
        </div>
    </form>
</div>

@push('js')
    <script>
        $(document).ready(function () {
            let form = $('.base-ajax form')

            $(document).on('change', '.auto-save-section [name]', function() {
                let section = $(this).parents('.base-ajax-section')
                let toggleInput = $(this).is(':checked')

                // Get form data (only for this section)
                let parentForm = $(section).parents('form')
                let array = $(section).find('*[name]').serializeArray()

                // Prepare formData object
                let formData = new FormData()
                $.each(array, function (key, el) {
                    formData.append(el.name, el.value)
                })

                formData.append('_method', $('[name="_method"]').val())
                formData.append('customer_id', $('[name="customer_id"]').val())
                formData.append('id', $('[name="order_id"]').val())

                if (section.find('[name="tags[]"]').length && !formData.has('tags[]')) {
                    formData.append('tags[]', '')
                }

                $.ajax({
                    type: 'POST',
                    url: parentForm.attr('action'),
                    enctype: parentForm.attr('enctype'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $(document).trigger('baseAjaxFormSuccess', [response])
                        reloadAuditLog()
                        toastr.success(response.message)
                    },
                    error: function (response) {
                        $(document).trigger('baseAjaxFormError', [response])
                        appendValidationMessages(section, response)
                        toastr.error(response.message)
                    }
                });

            })

            $(document).on('click', '.save-changes', function() {
                let section = $(this).parents('.base-ajax-section')
                let hasClassSaveNotes =  $(this).hasClass('save-notes')
                // Check if there is ckeditor
                window.updateCkEditorElements()

                // Get form data (only for this section)
                let parentForm = $(section).parents('form')
                let array = $(section).find('*[name]').serializeArray()

                // Prepare formData object
                let formData = new FormData()
                $.each(array, function (key, el) {
                    formData.append(el.name, el.value)
                })

                formData.append('_method', $('[name="_method"]').val())
                formData.append('customer_id', $('[name="customer_id"]').val())
                formData.append('id', $('[name="order_id"]').val())

                if (section.find('[name="tags[]"]').length && !formData.has('tags[]')) {
                    formData.append('tags[]', '')
                }

                $.ajax({
                    type: 'POST',
                    url: parentForm.attr('action'),
                    enctype: parentForm.attr('enctype'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $(document).trigger('baseAjaxFormSuccess', [response])
                        if(hasClassSaveNotes) {
                            updateCustomNote()
                        }

                        reloadAuditLog()
                        toastr.success(response.message)
                    },
                    error: function (response) {
                        $(document).trigger('baseAjaxFormError', [response])
                        appendValidationMessages(section, response)
                        toastr.error(response.message)
                    }
                });
            });

            $(document).on('click', '.edit-tr-section', function () {
                let section = $(this).parents('tr')
                section.addClass('editable')

                setReadonly(section)
                setReadonly(section, false)
                section.find('.picon-edit-filled').addClass('d-none');

            });

            $(document).on('click', '.save-cancel', function() {
                let section = $(this).parents('tr')
                section.removeClass('editable')

                setReadonly(section)
                setReadonly(section, true)
                showLoadingIcon(section, false)
                section.find('.picon-edit-filled').removeClass('d-none');

            })

            $(document).on('click', '.save-section .save', function() {
                let section = $(this).parents('tr')
                let saveIcon = $(this)
                // Hide save button
                saveIcon.addClass('d-none')

                // Remove error messages
                $(document).find('.form-error-messages').remove()

                window.updateCkEditorElements()

                // Get form data (only for this section)
                let parentForm = $(section).parents('form')
                let array = $(section).find('*[name]').serializeArray()

                // Prepare formData object
                let formData = new FormData()
                $.each(array, function (key, el) {
                    formData.append(el.name, el.value)
                })

                formData.append('_method', $('[name="_method"]').val())
                formData.append('customer_id', $('[name="customer_id"]').val())
                formData.append('id', $('[name="order_id"]').val())

                if (section.find('[name="tags[]"]').length && !formData.has('tags[]')) {
                    formData.append('tags[]', '')
                }

                //show loading icon
                showLoadingIcon(section, true)

                $.ajax({
                    type: 'POST',
                    url: parentForm.attr('action'),
                    enctype: parentForm.attr('enctype'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $(document).trigger('baseAjaxFormSuccess', [response])

                        saveIcon.removeClass('d-none')
                        section.removeClass('editable')
                        section.find('.picon-edit-filled').removeClass('d-none');

                        setReadonly(section, true)
                        updateEditorText(section)
                        showLoadingIcon(section, false)
                        showSaveSuccessIcon(section, true)

                        reloadAuditLog()
                        toastr.success(response.message)
                    },
                    error: function (response) {
                        $(document).trigger('baseAjaxFormError', [response])

                        saveIcon.removeClass('d-none')

                        showLoadingIcon(section, false)
                        showSaveErrorIcon(section, true)
                        setReadonly(section, false)
                        appendValidationMessages(section, response)

                        toastr.error(response.message)
                    }
                });
            })
        })

        function setReadonly(parent, readonly = true) {
            if (readonly) {
                parent.find('.save-section').addClass('d-none')
                parent.find('.for-edit').addClass('d-none')
                parent.find('.previewText').show()

                setTimeout(function () {
                    parent.find('.ck-editor').hide()
                    parent.find('textarea').show()
                }, 200)
            } else {
                parent.find('.save-section').removeClass('d-none')
                parent.find('.for-edit').removeClass('d-none')
                parent.find('.previewText').hide()

                setTimeout(function () {
                    parent.find('.ck-editor').show()
                    parent.find('textarea').hide()
                }, 200)
            }
        }

        function showLoadingIcon(section, show = true) {
            if (show) {
                return section.find('.loading').removeClass('d-none')
            }

            return section.find('.loading').addClass('d-none')
        }

        function showSaveSuccessIcon(section, show = true) {
            if (show) {
                return section.find('.saveSuccess').removeClass('d-none').css('display', 'block').fadeOut(5000)
            }

            return section.find('.saveSuccess').addClass('d-none')
        }

        function showSaveErrorIcon(section, show = true) {
            if (show) {
                return section.find('.saveError').removeClass('d-none').css('display', 'block').fadeOut(5000)
            }

            return section.find('.saveError').addClass('d-none')
        }

        function updateCustomNote() {
            var customNoteVal = $('textarea[name=note_text_append]').val();
            var checkedNote = $('input[name=note_type_append]:checked');
            var findTextArea = checkedNote.val()
            var parent = $('textarea[name='+findTextArea+']').parents('tr');
            var textarea = $('textarea[name='+findTextArea+']');
            if(textarea.val() != '') {
                textarea.val(function(i, text) {
                    return `${text} ${customNoteVal}`;
                });
            } else {
                textarea.val(customNoteVal);
            }
            updateEditorText(parent);
            $('textarea[name=note_text_append]').val('');
            parent.removeClass('d-none');
        }

        function updateEditorText(parent) {
            let textarea = parent.find('textarea');
            let textareaName = textarea.attr('name');
            if(textareaName == 'packing_note') {
                parent.find('.previewText').html(textarea.val())
            } else if(textareaName == 'gift_note') {
                parent.find('.previewText').html(textarea.val())
            } else if(textareaName == 'internal_note') {
                parent.find('.previewText').html(textarea.val())
            } else if(textareaName == 'slip_note') {
                parent.find('.previewText').html(textarea.val())
            }
        }

        $(document).on('click', '.globalSave', function (e) {
            // Stop default submit events
            e.preventDefault()
            e.stopPropagation()

            // Remove if there are error messages
            $(document).find('.form-error-messages').remove()

            // Append ckeditor data
            window.updateCkEditorElements()

            // Gather form data for the request
            let form = $('#' + $(this).data('form-id'))
            let formData = new FormData(form[0])

            if (form.find('[name="tags[]"]').length && !formData.has('tags[]')) {
                formData.append('tags[]', '')
            }

            // Append dropzone data if exists
            if (window.dropzoneInstance) {
                if (window.dropzoneInstance.getQueuedFiles().length > 0) {
                    $.each(window.dropzoneInstance.getQueuedFiles(), function (key, el) {
                        formData.append('file[]', el)
                    });
                }
            }

            // Send AJAX request
            $.ajax({
                method: 'POST',
                url: form.attr('action'),
                enctype: form.attr('enctype'),
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $(document).trigger('baseAjaxFormSuccess', [response])

                    if (formData.has('file[]')) {
                        $(document).trigger('dropzoneFileUploaded', [response])
                    }

                    // Handle store success
                    if (form.attr('method') === 'POST') {
                        toastr.success(response.message)

                        return setTimeout(function () {
                            window.location.href = form.data('success-redirect')
                        }, 1000)
                    }

                    // Handle update success
                    // Refresh page datatables after success
                    $('table[id$=-table]').each(function () {
                        $(window.dtInstances).each(function (key, dtInstance) {
                            dtInstance.ajax.reload()
                        })
                    })
                    setReadonly(form)
                    toastr.success(response.message)
                    $("html, body").animate({ scrollTop: 0 }, "slow")
                },
                error: function (response) {
                    $(document).trigger('baseAjaxFormError', [response])
                    appendValidationMessages(form, response)
                }
            })
        })

        $('.shipping_contact_info_set').click(function(){
            $('#cont_info_name').html($('#input-shipping_contact_information\\[name\\]').val());
            $('#cont_info_company_name').html($('#input-shipping_contact_information\\[company_name\\]').val());
            $('#cont_info_company_number').html($('#input-shipping_contact_information\\[company_number\\]').val());
            $('#cont_info_address').html($('#input-shipping_contact_information\\[address\\]').val());
            $('#cont_info_address2').html($('#input-shipping_contact_information\\[address2\\]').val());
            $('#cont_info_zip').html($('#input-shipping_contact_information\\[zip\\]').val());
            $('#cont_info_city').html($('#input-shipping_contact_information\\[city\\]').val());
            $('#cont_info_email').html($('#input-shipping_contact_information\\[email\\]').val());
            $('#cont_info_phone').html($('#input-shipping_contact_information\\[phone\\]').val());
            $('#cont_info_country_name').text($('[name="shipping_contact_information[country_id]"]').select2('data')[0].text);
            $('#cont_info_country_code').text($('[name="shipping_contact_information[country_id]"]').select2('data')[0].country_code);
        });
    </script>
@endpush
