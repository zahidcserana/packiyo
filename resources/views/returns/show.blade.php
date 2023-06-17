<div class="modal-content" id="show-return-modal">
    <div class="modal-header border-bottom mx-4 px-0">
        <h6 class="modal-title text-black text-left">
            {{ __('Return Details') }}
        </h6>

        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true" class="text-black">&times;</span>
        </button>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="form-group col-12">
                @include('shared.forms.input', [
                    'label' => __('Order'),
                    'type' => 'text',
                    'name' => '',
                    'value' => $return->order->number,
                    'readOnly' => true,
                ])
            </div>
        </div>
        <div class="row">
            <div class="form-group col-8">
                @include('shared.forms.input', [
                    'label' => __('Warehouse'),
                    'type' => 'text',
                    'name' => '',
                    'value' => $return->warehouse->information,
                    'readOnly' => true,
                ])
            </div>
            <div class="form-group col-4 {{ $errors->has('weight') ? 'has-danger' : '' }}">
                @include('shared.forms.input', [
                    'label' => __('Weight'),
                    'type' => 'text',
                    'name' => '',
                    'value' => $return->weight,
                    'readOnly' => true,
                ])
            </div>
        </div>
        <div class="row">
            <div class="form-group col-4 {{ $errors->has('width') ? ' has-danger' : '' }}">
                @include('shared.forms.input', [
                    'label' => __('Width'),
                    'type' => 'text',
                    'name' => '',
                    'value' => $return->width,
                    'readOnly' => true,
                ])
            </div>
            <div class="form-group col-4 {{ $errors->has('length') ? ' has-danger' : '' }}">
                @include('shared.forms.input', [
                    'label' => __('Length'),
                    'type' => 'text',
                    'name' => '',
                    'value' => $return->length,
                    'readOnly' => true,
                ])
            </div>
            <div class="form-group col-4 {{ $errors->has('height') ? ' has-danger' : '' }}">
                @include('shared.forms.input', [
                    'label' => __('Height'),
                    'type' => 'text',
                    'name' => '',
                    'value' => $return->height,
                    'readOnly' => true,
                ])
            </div>
        </div>
        <div class="row">
            @include('shared.forms.editSelectTag',
                [
                    'containerClass' => 'form-group col-4 mx-2 mb-0 text-left',
                    'labelClass' => '',
                    'selectClass' => 'select-ajax-tags',
                    'selectId' => '',
                    'label' => __('Tags'),
                    'minimumInputLength' => 3,
                    'default' => $return->tags,
                    'dropDownParent' => '#show-return-modal',
                    'disabled' => true
                ]
            )
        </div>
        <div class="row">
            <div class="col-12 table-responsive table-overflow">
                <table class="table table-normal bg-formWhite">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Image') }}</th>
                            <th scope="col">{{ __('Product') }}</th>
                            <th scope="col">{{ __('Quantity Shipped') }}</th>
                            <th scope="col">{{ __('Quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody id="order_items_container">
                        @foreach($return->items as $item)
                            <tr>
                                <td><img class="return_image_preview" src="{{ $item->product->productImages->first()->source ?? asset('img/inventory.svg') }}" alt=""></td>
                                <td>
                                    NAME: {{ $item->product->name }}
                                    <br>
                                    SKU: {{ $item->product->sku }}
                                </td>
                                <td>{{ $return->order->orderItems->where('product_id', $item->product_id)->first()->quantity }}</td>
                                <td>{{ $item->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mx-2">
                <h5 class="text-neutral-text-gray font-weight-600 font-xs">
                    Reason:
                </h5>

                <p class="font-xs">
                    {{ $return->reason }}
                </p>
            </div>
        </div>

        @if (isset($status))
            <form method="post" action="{{ route('return.statusUpdate', $return->id) }}" autocomplete="off" data-type="POST"
                  id="return-status-form" enctype="multipart/form-data"
            >
                @csrf
                @method('PUT')

                <div class="row mx--2 select2Container">
                    <div class="form-group col-12">
                        @include('shared.forms.ajaxSelect', [
                           'url' => route('return.filterStatuses'),
                           'name' => 'return_status_id',
                           'className' => 'ajax-user-input getFilteredStatuses',
                           'placeholder' => __('Search'),
                           'label' => __('Return status'),
                           'containerClass' => '',
                           'minInputLength' => 0,
                           'default' => [
                               'id' => $return->return_status_id,
                               'text' => $return->returnStatus->name ?? '',
                           ],
                        ])
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <button id="return-status-submit" class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 mt-5">
                        {{ __('Save') }}
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
