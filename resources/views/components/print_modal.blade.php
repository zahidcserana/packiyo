<div class="modal fade confirm-dialog" id="print-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header px-0">
                <div class="mx-4 pb-4 d-flex w-100">
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body text-white text-center overflow-auto pb-0 pt-0">
                <p class="text-black font-md font-weight-600 px-4">
                    {{ __('How many copies do you wish to print?') }}
                </p>
                @include('shared.forms.select', [
                   'name' => 'print_modal_printer_id',
                   'containerClass' => 'mb-2',
                   'label' => '',
                   'error' => false,
                   'value' => 'pdf',
                   'options' => ['pdf' => __('Generate PDF')]
                ])

                @include('shared.forms.input', [
                    'name' => 'print_modal_to_print',
                    'label' => '',
                    'labelClass' => 'd-none',
                    'value' => 1,
                    'type' => 'number',
                    'containerClass' => 'mb-0',
                    'class' => 'text-center',
                ])
            </div>
            <div class="modal-footer mx-auto">
                <button
                    id="print-submit"
                    type="button"
                    class="confirm-button btn bg-logoOrange px-5 font-weight-700 text-sm change-tab text-white"
                    data-dismiss="modal"
                    data-submit-action=""
                >
                    {{ __('Submit') }}
                </button>
            </div>
        </div>
    </div>
</div>

@include('shared.modals.components.product.print_pdf')

<script>
    $(document).on('show.bs.modal', '#print-modal', function (e) {
        $.ajax({
            method: 'GET',
            url: $(e.relatedTarget).data('customer-printers-url'),
            success: function (response) {
                let printers = [{id: 'pdf', text: 'Generate PDF'}]

                Object.entries(response.data)
                      .forEach(([key, value]) => {
                          printers.push({id: key, text: value})
                      });

                $('select[name="print_modal_printer_id"]').empty().select2({data: printers})
            }
        })

        $(this).find('[name="print_modal_to_print"]').val(1)
        $(this).find('#print-submit')
               .data('submit-action', $(e.relatedTarget).data('submit-action'))
               .data('pdf-url', $(e.relatedTarget).data('pdf-url'))
    })

    $(document).on('click', '#print-submit', function () {
        let to_print = $('[name="print_modal_to_print"]').val()
        let printer_id = $('[name="print_modal_printer_id"] option:selected').val()

        if (printer_id === 'pdf') {
            $('#print-pdf .pdf-url > a').attr('href', $(this).data('pdf-url'))
            $('#print-pdf').modal('show')

            return
        }

        $.ajax({
            method: 'POST',
            url: $(this).data('submit-action'),
            data: {
                to_print: to_print,
                printer_id: printer_id,
            },
            success: function (data) {
                toastr.success(data.message)
            }
        })
    })
</script>
