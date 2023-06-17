<form method="post" action="{{ route('product.update', [ 'product' => $product ]) }}" id="product-form" autocomplete="off" class="productForm modal-content d-none" data-type="PUT" enctype="multipart/form-data">
    <div class="modal-header border-bottom mx-4 px-0">
        <h6 class="modal-title text-black text-left" id="modal-title-notification">{{ __('Edit product') }}</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
            <span aria-hidden="true" class="text-black">&times;</span>
        </button>
    </div>
    <div class="modal-body text-center py-3 overflow-auto">
        @csrf
        <div class="d-flex justify-content-md-between inputs-container flex-column">
            {{ method_field('PUT') }}
            <div class="productEditDropzone w-100 p-2">
                @include('shared.forms.dropzone',
                    [
                        'url' => route('product.update', ['product' => $product]),
                        'isMultiple' => true,
                        'dropzoneId' => 'dropzone-body-edit',
                        'formId' => 'product-form',
                        'buttonId' => 'submit-button',
                        'images' => $product->productImages
                    ])
            </div>
            <div class="d-flex mt-3 flex-column productInputs pb-2 ">
                <div class="d-flex">
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="sku" >{{ __('SKU') }} </label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('SKU') }}"
                                type="text"
                                name="sku"
                                value="{{ $product->sku ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="height">{{ __('Height(inches)') }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Height(inches)') }}"
                                type="number"
                                name="height"
                                value="{{ $product->height ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="hs_code">{{ __('HS Code') }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('HS Code') }}"
                                type="text"
                                name="hs_code"
                                value="{{ $product->hs_code ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="name">{{ __('Name') }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Name') }}"
                                type="text"
                                name="name"
                                value="{{ $product->name ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="length">{{ __('Length(inches)') }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Length(inches)') }}"
                                type="text"
                                name="length"
                                value="{{ $product->length ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="quantity_on_hand">{{ __('Quantity on hand') }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Quantity on hand') }}"
                                type="text"
                                name="quantity_on_hand"
                                value="{{ $product->quantity_on_hand ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="barcode">{{ __('Barcode(UPC or other)') }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Barcode(UPC or other)') }}"
                                type="text"
                                name="barcode"
                                value="{{ $product->barcode ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="replacement">{{ __('Replacement Value') . ' (' . (isset($sessionCustomer) ? $sessionCustomer->currency : '') . ')' }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Replacement Value') . ' (' . (isset($sessionCustomer) ? $sessionCustomer->currency : '') . ')' }}"
                                type="text"
                                name="replacement"
                                value="{{ $product->replacement ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="quantity_allocated">{{ __('Quantity allocated') }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Quantity allocated') }}"
                                type="text"
                                name="quantity_allocated"
                                value="{{ $product->quantity_allocated ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="customs_price">{{ __('Price') }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Price') }}"
                                type="text"
                                name="price"
                                value="{{ $product->price ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="custom_price">{{ __('Custom Price') }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Custom Price') }}"
                                type="text"
                                name="custom_price"
                                value="{{ $product->custom_price ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="quantity_available">{{ __('Quantity available') }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Quantity available') }}"
                                type="text"
                                name="quantity_available"
                                value="{{ $product->quantity_available ?? '' }}">
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="type">{{ __('Weight (inches)') }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Weight (inches)') }}"
                                type="text"
                                name="weight"
                                value="{{ $product->weight ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="width">{{ __('Width(inches)') }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <input
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                placeholder="{{ __('Width(inches)') }}"
                                type="text"
                                name="width"
                                value="{{ $product->width ?? '' }}">
                        </div>
                    </div>
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs"
                               data-id="country_of_origin">{{ __('Country of origin') }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <select
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2 country-select"
                                type="text"
                                name="country_of_origin">
                                @if((! empty($countries)) && count($countries))
                                    @foreach($countries as $country)
                                        <option {{ ($product->country_of_origin === $country->id) ? 'selected' : '' }} value="{{ $country->id}} ">
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs" data-id="type">{{ __('Product type') }}</label>
                        <div
                            class="input-group input-group-alternative input-group-merge tableSearch">
                            <select
                                class="form-control font-weight-600 text-neutral-gray h-auto p-2 type-select"
                                type="text"
                                name="is_kit">
                                <option value="0" {{ (! empty($product) &&  count($product->kitItems)) ? '' : 'selected' }}>{{ __('Regular') }}</option>
                                <option value="1" {{ (! empty($product) &&  count($product->kitItems)) ? 'selected' : '' }}>{{ __('Kit') }}</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            <div class="p-2">
                @include('shared.forms.kitProductInput', [
                    'url' => route('product.filterKitProducts'),
                    'className' => 'ajax-user-input send-filtered-request',
                    'placeholder' => 'Search',
                    'label1' => ('Product'),
                    'label2' => __('Quantity'),
                    'visible' => (! empty($product) &&  count($product->kitItems)),
                    'defaults' => $product->kitItems ?? ''
                ])
            </div>
            <div class="mx-2 border-bottom-gray mb-1">
                <h6 class="heading-small text-muted text-left mb-2">{{ __('Vendor Information') }}</h6>
            </div>
            <div class="supplier_container m-2">
                <h6 class="heading-small text-muted mb-4">{{ __('Suppliers') }}</h6>
                <table class="col-12 table align-items-center table-flush">
                    <tbody id="supplier_container"
                           data-className="ajax-user-input"
                           data-url="{{ route('product.filterSuppliers') }}"
                           data-placeholder="{{ __('Search') }}">
                    @if(! empty($product->suppliers) && count($product->suppliers))
                        @foreach($product->suppliers as $supplier)
                            <tr>
                                <td  style="white-space: unset">
                                    <select
                                        name="suppliers[]"
                                        class="ajax-user-input getFilteredSuppliers"
                                        data-ajax--url="{{ route('product.filterSuppliers') }}"
                                        data-placeholder="{{ __('Search') }}"
                                        data-minimum-input-length="1"
                                        data-toggle="select">
                                        <option value="{{ $supplier->id }}">{{ $supplier->contactInformation->name . ', ' . $supplier->contactInformation->email . ', ' . $supplier->contactInformation->zip . ', ' . $supplier->contactInformation->city . ', ' . $supplier->contactInformation->phone }}</option>
                                    </select>
                                </td>
                                <td class="delete-row">
                                    <div>
                                        <i class="fas fa-trash-alt text-lightGrey"></i>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td style="white-space: unset">
                                @include('shared.forms.ajaxSelect', [
                                    'url' => route('product.filterSuppliers'),
                                    'name' => 'suppliers[]',
                                    'className' => 'ajax-user-input getFilteredSuppliers',
                                    'placeholder' => __('Search'),
                                    'labelClass' => 'd-none',
                                    'containerClass' => 'mb-0',
                                    'label' => ''
                                ])
                            </td>
                            <td class="delete-row">
                                <div><i class="fas fa-trash-alt text-lightGrey"></i></div>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn bg-logoOrange text-white w-100 my-4 font-weight-700 fa fa-plus mt-3 w-100" id="add_item"></button>
                </div>
            </div>
        </div>
        <div class="form-group mb-0 mx-2 text-left mb-3">
            <label for=""
                   class="text-neutral-text-gray font-weight-600 font-xs" data-id="notes">{{ __('Notes') }}</label>
            <textarea name="notes"
                      class="px-4 text-black">
                           {!! $product->notes !!}
                        </textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button"
                class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 confirm-button" id="submit-button">{{ __('Save') }}</button>
    </div>
</form>
