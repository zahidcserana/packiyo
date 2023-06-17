<form method="post" action="{{ route('supplier.update', [ 'supplier' => $supplier, 'id' => $supplier->id ]) }}" autocomplete="off" data-type="POST" id="supplier-edit-form" enctype="multipart/form-data"
      class="modal-content supplierForm">
    @csrf
    {{ method_field('PUT') }}
    <div class="modal-header px-0">
        <div class="mx-4 pb-4 d-flex w-100 border-bottom-gray">
            <h6 class="modal-title text-black text-left"
                id="modal-title-notification">{{ __('Edit vendor') }}</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                <span aria-hidden="true" class="text-black">&times;</span>
            </button>
        </div>
    </div>
    <div class="modal-body text-center py-3 overflow-auto">
        @include('supplier.supplierInformationFields', [
            'supplier' => $supplier
        ])
        <input type="hidden" name="customer_id" value="{{ $supplier->customer->id }}" class="customer_id" />
        <br>
        <div class="searchSelect">
            <label data-id="purchase_order_items"></label>
            @include('shared.forms.ajaxSelect', [
                'url' => route('supplier.filterProducts', ['customer' => $supplier->customer]),
                'name' => 'supplier[0][product_id]',
                'className' => 'ajax-user-input product_id',
                'placeholder' => __('Search products'),
                'label' => '',
                'labelClass' => 'd-block',
                'fixRouteAfter' => '.ajax-user-input.customer_id'
            ])
        </div>
        @include('supplier.supplierProductsInformation', [
            'products' => $supplier->products ?? []
        ])
    </div>
    <div class="modal-footer">
        <button type="button"
                data-toggle="modal" data-target="#vendorDeleteModal" data-token="{{ csrf_token() }}" data-url="{{ route('supplier.destroy', ['id' => $supplier->id, 'supplier' => $supplier]) }}"
                class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700">
            {{ __('Delete') }}
        </button>
        <button type="submit"
                class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 confirm-button modal-submit-button"
                id="submit-button">
            {{ __('Save') }}
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        let customerEditSelect = $('.customer-edit');

        customerEditSelect.select2({
            dropdownParent: $('#vendorEditModal')
        });

        $('.modal-submit-button').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            $(document).find('.form-error-messages').remove()

            let _form = $(this).closest('.supplierForm');
            let form = _form[0];
            let formData = new FormData(form);

            $.ajax({
                type: 'POST',
                url: _form.attr('action'),
                headers: {'X-CSRF-TOKEN': formData.get('_token')},
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#vendorEditModal').modal('toggle');

                    toastr.success(data.message)

                    dtInstances['#supplier-table'].ajax.reload()
                },
                error: function (response) {
                    appendValidationMessages(
                        $('#vendorEditModal'),
                        response
                    )
                }
            });
        });
    });

    $('#vendorDeleteModal').on('show.bs.modal', function (e) {
        let deleteURL = $(e.relatedTarget).data('url')
        let token = $(e.relatedTarget).data('token')

        $('#deleteVendorForm').attr('action', deleteURL)
        $('#formToken').attr('value', token)
    })

    if (typeof searchSelect === 'undefined') {
        let searchSelect = $('.searchSelect .product_id');

        searchSelect.select2({
            dropdownParent: $("#vendorEditModal"),
            ajax: {
                processResults: function (data, params) {
                    return  {
                        results: data.results,
                    }
                }
            },
        })

        searchSelect.on('select2:select', function (e) {
            let data = e.params.data;

            let productRow = `<tr>`+
                `<input type="hidden" name="product_id[]" value="` + data.id + `">` +
                `<td class="py-4 text-black font-weight-600 font-sm">` + data.name + `</td>`+
                `<td class="py-4 text-black font-weight-600 font-sm">` + data.sku + `</td>`+
                `<td class="py-4 text-black font-weight-600 font-sm">` + data.price + `</td>`+
                `<td class="py-4 text-black font-weight-600 font-sm">` + data.quantity + `</td>`+
                `</tr>`;

            $('.empty-products-table').remove()
            $('#product_container').append(productRow)
            searchSelect.val(null).trigger('change');
        });
    }
</script>
