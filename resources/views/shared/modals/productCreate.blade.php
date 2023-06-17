<div class="modal fade confirm-dialog" id="productCreateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form method="post" action="{{ route('product.store') }}" autocomplete="off" data-type="POST" id="product-create-form" enctype="multipart/form-data"
              class="modal-content productForm">
            <div class="modal-header px-0">
                <div class="mx-4 pb-4 d-flex w-100 border-bottom-gray">
                    <h6 class="modal-title text-black text-left"
                        id="modal-title-notification">{{ __('Create product') }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                        <span aria-hidden="true" class="text-black">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body text-white text-center pb-3 pt-0 overflow-auto">
                @csrf
                <div class="nav-wrapper">
                    <ul class="nav nav-pills nav-fill flex-md-row" id="tabs-icons-text" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0 active" id="product-information-tab" data-toggle="tab"
                               href="#product-information-tab-content" role="tab" aria-controls="tabs-icons-text-1"
                               aria-selected="true">
                                {{ __('Product Information') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0" id="product-international-orders-tab" data-toggle="tab"
                               href="#product-international-orders-tab-content" role="tab"
                               aria-controls="tabs-icons-text-2" aria-selected="false">
                                {{ __('International Orders') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0" id="product-vendors-information-tab" data-toggle="tab"
                               href="#product-vendors-information-tab-content" role="tab"
                               aria-controls="tabs-icons-text-3" aria-selected="false">
                                {{ __('Vendors Information') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0" id="product-upload-image-tab" data-toggle="tab"
                               href="#product-upload-image-tab-content" role="tab" aria-controls="tabs-icons-text-1"
                               aria-selected="true">
                                {{ __('Upload Image') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content text-black" id="myTabContent">
                    <div class="tab-pane fade productInputs show active" id="product-information-tab-content" role="tabpanel"
                         aria-labelledby="product-information-tab">
                        <div class="d-flex flex-column">
                            @if(!isset($sessionCustomer))
                                <div class="searchSelect flex-grow-1">
                                    @include('shared.forms.new.ajaxSelect', [
                                    'url' => route('user.getCustomers'),
                                    'name' => 'customer_id',
                                    'className' => 'ajax-user-input customer_id',
                                    'placeholder' => __('Select customer'),
                                    'label' => __('Customer'),
                                    'default' => [
                                        'id' => old('customer_id'),
                                        'text' => ''
                                    ],
                                    'fixRouteAfter' => '.ajax-user-input.customer_id'
                                ])
                                </div>
                            @else
                                <input type="hidden" name="customer_id" value="{{ $sessionCustomer->id }}" />
                            @endif
                            <div class="d-md-flex">
                                <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                                    <label for=""
                                           class="text-neutral-text-gray font-weight-600 font-xs"
                                           data-id="sku">{{ __('SKU') }} </label>
                                    <div
                                        class="input-group input-group-alternative input-group-merge tableSearch">
                                        <input
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                            placeholder="{{ __('SKU') }}"
                                            type="text"
                                            name="sku">
                                    </div>
                                </div>
                                <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                                    <label for=""
                                           class="text-neutral-text-gray font-weight-600 font-xs"
                                           data-id="name">{{ __('Name') }}</label>
                                    <div
                                        class="input-group input-group-alternative input-group-merge tableSearch">
                                        <input
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                            placeholder="{{ __('Name') }}"
                                            type="text"
                                            name="name">
                                    </div>
                                </div>
                                <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                                    <label for=""
                                           class="text-neutral-text-gray font-weight-600 font-xs"
                                           data-id="barcode">{{ __('Barcode(UPC or other)') }}</label>
                                    <div
                                        class="input-group input-group-alternative input-group-merge tableSearch">
                                        <input
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                            placeholder="{{ __('Barcode(UPC or other)') }}"
                                            type="text"
                                            name="barcode">
                                    </div>
                                </div>
                            </div>
                            <div class="d-md-flex">
                                <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                                    <label for=""
                                           class="text-neutral-text-gray font-weight-600 font-xs"
                                           data-id="price">{{ __('Price') }}
                                            <span class="currency-label"></span>
                                    </label>
                                    <div
                                        class="input-group input-group-alternative input-group-merge tableSearch">
                                        <input
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                            placeholder="{{ __('Price') }}"
                                            type="text"
                                            name="price">
                                    </div>
                                </div>
                                <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                                    <label for=""
                                           class="text-neutral-text-gray font-weight-600 font-xs"
                                           data-id="weight">{{ __('Weight') }}
                                            <span class="weight-label"></span>
                                    </label>
                                    <div
                                        class="input-group input-group-alternative input-group-merge tableSearch">
                                        <input
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                            placeholder="{{ __('Weight') }}"
                                            type="text"
                                            name="weight">
                                    </div>
                                </div>
                                <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                                    <label for=""
                                           class="text-neutral-text-gray font-weight-600 font-xs"
                                           data-id="width">{{ __('Width') }}
                                            <span class="dimensions-label"></span>
                                    </label>
                                    <div
                                        class="input-group input-group-alternative input-group-merge tableSearch">
                                        <input
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                            placeholder="{{ __('Width') }}"
                                            type="text"
                                            name="width">
                                    </div>
                                </div>
                            </div>
                            <div class="d-md-flex">
                                <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                                    <label for="height"
                                           class="text-neutral-text-gray font-weight-600 font-xs"
                                           data-id="height">{{ __('Height') }}
                                            <span class="dimensions-label"></span>
                                    </label>
                                    <div
                                        class="input-group input-group-alternative input-group-merge tableSearch">
                                        <input
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                            placeholder="{{ __('Height') }}"
                                            type="number"
                                            id="height"
                                            name="height">
                                    </div>
                                </div>
                                <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                                    <label for=""
                                           class="text-neutral-text-gray font-weight-600 font-xs"
                                           data-id="length">{{ __('Length') }}
                                            <span class="dimensions-label"></span>
                                    </label>
                                    <div
                                        class="input-group input-group-alternative input-group-merge tableSearch">
                                        <input
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                            placeholder="{{ __('Length') }}"
                                            type="text"
                                            name="length">
                                    </div>
                                </div>
                                <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                                    <label for=""
                                           class="text-neutral-text-gray font-weight-600 font-xs"
                                           data-id="replacement">{{ __('Replacement Value') }}
                                            <span class="currency-label"></span>
                                    </label>
                                    <div
                                        class="input-group input-group-alternative input-group-merge tableSearch">
                                        <input
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                            placeholder="{{ __('Replacement Value') }}"
                                            type="text"
                                            name="value">
                                    </div>
                                </div>


                            </div>
                            <div class="d-md-flex">
                                <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1 searchSelect">
                                    <label for="is_kit" class="text-neutral-text-gray font-weight-600 font-xs" data-id="type">
                                        {{ __('Product type') }}
                                    </label>
                                    <div class="input-group input-group-alternative input-group-merge tableSearch">
                                        <select
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2 type-select"
                                            type="text"
                                            name="is_kit"
                                            id="is_kit"
                                        >
                                            <option value="{{ \App\Models\Product::PRODUCT_TYPE_REGULAR }}" selected>{{ __('Regular') }}</option>
                                            <option value="{{ \App\Models\Product::PRODUCT_TYPE_STATIC_KIT }}">{{ __('Static Kit') }}</option>
{{--                                            <option value="{{ \App\Models\Product::PRODUCT_TYPE_DYNAMIC_KIT }}">{{ __('Dynamic Kit') }}</option>--}}
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                                    <label for="reorder_threshold"
                                           class="text-neutral-text-gray font-weight-600 font-xs"
                                           data-id="reorder_threshold">{{ __('Reorder threshold') }}</label>
                                    <div
                                        class="input-group input-group-alternative input-group-merge tableSearch">
                                        <input
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                            placeholder="{{ __('Reorder threshold') }}"
                                            type="number"
                                            id="reorder_threshold"
                                            name="reorder_threshold">
                                    </div>
                                </div>
                                <div class="form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                                    <label for="quantity_reorder"
                                           class="text-neutral-text-gray font-weight-600 font-xs"
                                           data-id="quantity_reorder">{{ __('Quantity reorder') }}</label>
                                    <div
                                        class="input-group input-group-alternative input-group-merge tableSearch">
                                        <input
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                            placeholder="{{ __('Quantity reorder') }}"
                                            type="number"
                                            id="quantity_reorder"
                                            name="quantity_reorder">
                                    </div>
                                </div>
                            </div>
                            <div class="d-md-flex">
                                @include('shared.forms.editSelectTag', [
                                    'containerClass' => 'form-group mb-0 position-relative mx-2 text-left mb-3 flex-grow-1',
                                    'labelClass' => '',
                                    'selectClass' => 'select-ajax-tags',
                                    'label' => __('Tags'),
                                    'minimumInputLength' => 3,
                                    'dropDownParent' => '#productCreateModal',
                                    'default' => []
                                ])
                            </div>
                            <div class="custom-form-checkbox priority-counting-checkbox position-relative font-weight-600 form-group mb-0 mx-2 text-left mb-3 flex-grow-1">
                                <div>
                                    <input class="" name="has_serial_number" id="chk-has_serial_number" type="checkbox" value="1">
                                    <label class="text-black font-weight-600" for="chk-has_serial_number">{{ __('Needs Serial Number') }}</label>
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
                                'visible' => ! empty($product) &&  count($product->kitItems),
                                'defaults' => $product->kitItems ?? ''
                            ])
                        </div>
                        <button type="button" class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 change-tab text-white" data-id="#product-international-orders-tab">
                            {{ __('Next') }}
                        </button>
                    </div>
                    <div class="tab-pane fade productInputs" id="product-international-orders-tab-content" role="tabpanel"
                         aria-labelledby="product-international-orders-tab">
                        <div class="row">
                            <div class="col">
                                <div class="form-group mb-0 mx-2 text-left mb-3">
                                    <label for=""
                                           class="text-neutral-text-gray font-weight-600 font-xs"
                                           data-id="customs_price">{{ __('Customs price') }}
                                            <span class="currency-label"></span>
                                    </label>
                                    <div
                                        class="input-group input-group-alternative input-group-merge tableSearch">
                                        <input
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                            placeholder="{{ __('Customs price') }}"
                                            type="text"
                                            name="customs_price">
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0 mx-2 text-left mb-3 searchSelect">
                                    @include('shared.forms.countrySelect', [
                                        'label' => __('Country of origin'),
                                        'name' => 'country_of_origin'
                                    ])
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group mb-0 mx-2 text-left mb-3">
                                    <label for=""
                                           class="text-neutral-text-gray font-weight-600 font-xs"
                                           data-id="country">{{ __('HS Code') }}</label>
                                    <div
                                        class="input-group input-group-alternative input-group-merge tableSearch">
                                        <input
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                            placeholder="{{ __('HS Code') }}"
                                            type="text"
                                            name="hs_code">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group mb-0 mx-2 text-left mb-3">
                                    <label for="" class="text-neutral-text-gray font-weight-600 font-xs" data-id="customs_description">{{ __('Customs Description') }}</label>
                                    <div class="input-group input-group-alternative input-group-merge tableSearch">
                                        <input
                                            class="form-control font-weight-600 text-neutral-gray h-auto p-2"
                                            placeholder="{{ __('Customs Description') }}"
                                            type="text"
                                            name="customs_description">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 change-tab text-white" data-id="#product-vendors-information-tab">
                            {{ __('Next') }}
                        </button>
                    </div>
                    <div class="tab-pane fade" id="product-vendors-information-tab-content" role="tabpanel"
                         aria-labelledby="product-vendors-information-tab">
                        <div class="w-100">
                            <div class="form-group mb-0 mx-2 text-left mb-3">
                                <div class="">
                                    <div class="table-responsive table-overflow supplier_container">
                                        <h6 class="heading-small text-muted mb-4">{{ __('Suppliers') }}</h6>
                                        <table class="col-12 table align-items-center table-flush">
                                            <tbody id="supplier_container"
                                                   data-className="ajax-user-input"
                                                   data-url="{{ route('product.filterSuppliers') }}"
                                                   data-placeholder="{{ __('Search') }}">
                                                <tr>
                                                    <td style="white-space: unset">
                                                        @include('shared.forms.ajaxSelect', [
                                                            'url' => route('product.filterSuppliers'),
                                                            'name' => 'suppliers[]',
                                                            'className' => 'ajax-user-input getFilteredSuppliers',
                                                            'placeholder' => __('Search Supplier'),
                                                            'labelClass' => 'd-none',
                                                            'minInputLength' => 0,
                                                            'containerClass' => 'mb-0',
                                                            'label' => ''
                                                        ])
                                                    </td>
                                                    <td class="delete-row">
                                                        <div><i class="fas fa-trash-alt text-lightGrey"></i></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" class="btn bg-logoOrange text-white w-100 my-4 font-weight-700 fa fa-plus mt-3 w-100" id="add_item"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn bg-logoOrange mx-auto px-5 font-weight-700 mt-5 change-tab text-white" data-id="#product-upload-image-tab">
                            {{ __('Next') }}
                        </button>
                    </div>
                    <div class="tab-pane fade" id="product-upload-image-tab-content" role="tabpanel"
                         aria-labelledby="product-upload-image-tab">
                        <div class="container px-0 productCreateDropzone">
                            <label for=""
                                   class="text-neutral-text-gray font-weight-600 font-xs" data-id="file"></label>
                            @include('shared.forms.dropzone',
                            [
                                'url' => route('product.store'),
                                'isMultiple' => true,
                                'formId' => 'product-create-form',
                                'buttonId' => 'submit-create-button',
                                'images' => null,
                                'dropzoneId' => 'dropzone-body-create',
                                'name' => 'file[]'
                            ])
                        </div>
                        <button type="button" class="btn bg-logoOrange text-white mx-auto px-5 font-weight-700 confirm-button mt-5" id="submit-create-button">
                            {{ __('Create') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
