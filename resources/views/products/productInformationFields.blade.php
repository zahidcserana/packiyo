@include('shared.forms.input', [
   'name' => 'sku',
   'label' => __('Sku'),
   'value' => $product->sku ?? '',
   'readOnly' => $product->sku ?? '' ? 'readonly' : ''
])
@include('shared.forms.input', [
    'name' => 'name',
    'label' => __('Name'),
    'value' => $product->name ?? ''
])
@include('shared.forms.input', [
    'name' => 'price',
    'label' => __('Price'),
    'value' => $product->price ?? ''
])
@include('shared.forms.input', [
    'name' => 'notes',
    'label' => __('Notes'),
    'value' => $product->notes ?? ''
])
@include('shared.forms.productDimensions', [
    'product' => $product ?? null
])
@include('shared.forms.input', [
    'name' => 'barcode',
    'label' => __('Barcode'),
    'value' => $product->barcode ?? ''
])
@include('shared.forms.dropdowns.customer_selection', [
    'route' => route('product.filterCustomers'),
    'readonly' => isset($product->customer->id) ? 'true' : null,
    'id' => $product->customer->id ?? old('customer_id'),
    'text' => $product->customer->contactInformation->name ?? ''
])
@include('shared.forms.checkbox', [
    'name' => 'is_kit',
    'label' => ('Kit Product'),
    'checked' => ! empty($product) && count($product->kitItems),
    'value' => true
])
@include('shared.forms.kitProductInput', [
    'url' => route('product.filterKitProducts'),
    'className' => 'ajax-user-input send-filtered-request',
    'placeholder' => 'Search',
    'label1' => ('Product'),
    'label2' => __('Quantity'),
    'visible' => ! empty($product) &&  count($product->kitItems),
    'defaults' => $product->kitItems ?? ''
])
@include('shared.forms.checkbox', [
    'name' => 'lot_tracking',
    'label' => ('Needs Lot Tracking'),
    'checked' => ! empty($product) && count($product->lot_tracking),
    'value' => true
])
<div class="pl-lg-4">
    <div class="table-responsive">
        <h6 class="heading-small text-muted mb-4">{{ __('Suppliers') }}</h6>
        <table class="col-12 table align-items-center table-flush">
            <thead class="thead-light">
            <tr>
                <th scope="col">{{ __('Supplier') }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody id="supplier_container"
                   data-className="ajax-user-input"
                   data-url="{{ route('product.filterSuppliers') }}"
                   data-placeholder="{{ __('Search') }}"
            >

            @if(count( $product->suppliers ?? [] ) > 0)
                @foreach( $product->suppliers as $key => $supplier)
                    <tr>
                        <td style="white-space: unset">
                            @include('shared.forms.ajaxSelect', [
                                'url' => route('product.filterSuppliers'),
                                'name' => 'suppliers[]',
                                'className' => 'ajax-user-input',
                                'placeholder' => __('Search'),
                                'labelClass' => 'd-none',
                                'containerClass' => 'mb-0',
                                'label' => '',
                                'default' => [
                                    'id' => $supplier->id,
                                    'text' => $supplier->name
                                 ]
                            ])
                        </td>
                        <td>
                            <button class="delete-row  btn btn-danger fa fa-trash"></button>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td style="white-space: unset">
                        @include('shared.forms.ajaxSelect', [
                            'url' => route('product.filterSuppliers'),
                            'name' => 'suppliers[]',
                            'className' => 'ajax-user-input',
                            'placeholder' => __('Search'),
                            'labelClass' => 'd-none',
                            'containerClass' => 'mb-0',
                            'label' => ''
                        ])
                    </td>
                    <td>
                        <button class="delete-row  btn btn-danger fa fa-trash"></button>
                    </td>
                </tr>
            @endif
            </tbody>
        </table>
        <button id="add_item" type="button" class="btn btn-success mt-4">{{ __('Add more items') }}</button>
    </div>
</div>
