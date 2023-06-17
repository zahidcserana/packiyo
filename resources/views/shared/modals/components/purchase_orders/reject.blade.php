<form method="post" action="{{ route('purchase_order.reject', compact('purchaseOrderItem')) }}" autocomplete="off" data-type="POST" id="purchase-order-item-reject-form" enctype="multipart/form-data"
      class="modal-content purchaseOrderItemRejectForm">
    @csrf
    <div class="modal-header px-0">
        <div class="mx-4 pb-4 d-flex w-100 border-bottom-gray">
            <h6 class="modal-title text-black text-left"
                id="modal-title-notification">{{ __('Rejected item') }}</h6>
            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                <span aria-hidden="true" class="text-black">&times;</span>
            </button>
        </div>
    </div>
    <div class="modal-body text-center py-3 overflow-auto">
        <div class="d-flex justify-content-md-between inputs-container">
            <div class="w-100">
                <table class="table align-items-center col-12 items-table" id="purchase-order-item-table">
                    <thead>
                    <tr class="text-center">
                        <th>{{ __('Image') }}</th>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Price') }}</th>
                    </tr>
                    </thead>
                    <tbody style="cursor:pointer">
                        <tr class="text-center">
                            <td>
                                @if (empty($purchaseOrderItem->product->productImages[0]))
                                    <img src="{{ asset('img/no-image.png') }}" alt="No image">
                                @else
                                    <img src="{{ $purchaseOrderItem->product->productImages[0]->source }}" width="30%">
                                @endif
                            </td>
                            <td>
                                <span>
                                Name: {{ $purchaseOrderItem->product->name }}
                            </span>
                                <br>
                                <span>
                                SKU: {{ $purchaseOrderItem->product->sku }}
                            </span>
                            </td>
                            <td>
                                {{ $purchaseOrderItem->product->price }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        <div class="d-flex justify-content-md-between inputs-container">
            <div class="w-100">
                <table class="table align-items-center col-12 items-table items-table" id="purchase-order-item-table">
                    <thead>
                    <tr class="text-center">
                        <th>Quantity</th>
                        <th>Reason</th>
                        <th>Note</th>
                    </tr>
                    </thead>
                    <tbody style="cursor:pointer" id="item_container">
                    @if(!empty($rejectedItems))
                        @foreach($rejectedItems as $rejectedItem)
                            <tr class="text-center">
                                <td>
                                    <span class="text-black text-right font-sm font-weight-600">{{ $rejectedItem->quantity }}</span>
                                </td>
                                <td>
                                    <span class="text-black text-right font-sm font-weight-600">{{ $rejectedItem->reason }}</span>
                                </td>
                                <td>
                                    <span class="text-black text-right font-sm font-weight-600">{{ $rejectedItem->note }}</span>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    <tr class="text-center purchase-order-item-fields">
                        <td>
                            <div class="form-group mb-0 mx-2 text-left">

                                <div
                                    class="input-group input-group-alternative input-group-merge">
                                    <input
                                        class="form-control font-weight-600 h-auto p-2 w-100"
                                        type="number"
                                        min="0"
                                        name="quantity[0]"
                                        value="0"
                                    >
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="form-group mb-0 mx-2 text-left">

                                <div
                                    class="input-group input-group-alternative input-group-merge">
                                    <input
                                        class="form-control font-weight-600 h-auto p-2 w-100"
                                        type="text"
                                        name="reason[0]"
                                        placeholder="Reason for rejection"
                                    >
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="form-group mb-0 mx-2 text-left">
                                <div
                                    class="input-group input-group-alternative input-group-merge">
                                    <textarea
                                        rows="1"
                                        class="form-control font-weight-600 p-2 w-100"
                                        name="note[0]"
                                    ></textarea>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <button type="button"
                class="btn bg-logoOrange text-white w-100 my-4 font-weight-700 mt-3 w-100 border-8"
                id="add-new-reason">
            <i class="fa fa-plus"></i>
            {{ __('Add new reason') }}
        </button>
    </div>
    <div class="modal-footer">
        <button type="submit"
                class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 confirm-button modal-submit-button"
                id="submit-button">
            {{ __('Save') }}
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('.modal-submit-button').click(function (e) {
            e.preventDefault();
            e.stopPropagation();

            $(document).find('.form-error-messages').remove()

            let _form = $(this).closest('.purchaseOrderItemRejectForm');
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
                    $('#quantityRejectedModal').modal('toggle');

                    toastr.success(data.message)

                    window.location.reload()
                },
                error: function (response) {
                    appendValidationMessages(
                        $('#quantityRejectedModal'),
                        response
                    )
                }
            });
        });

        $('#add-new-reason').click(function (event) {
            event.preventDefault();
            const lastItemField = $('.purchase-order-item-fields:last');

            let lastItemFieldHTML = lastItemField[0].outerHTML;
            let index = lastItemFieldHTML.match(/\[(\d+?)\]/);
            let orderItemFields = $(lastItemFieldHTML.replace(/\[\d+?\]/g, '[' + (parseInt(index[1]) + 1) + ']'));

            $('#item_container').append(orderItemFields);
        });
    });
</script>
