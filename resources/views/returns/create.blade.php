@extends('layouts.app')

@section('content')
    @include('layouts.headers.auth', [
        'title' => 'Returns',
        'subtitle' =>  __('Create Return')
    ])
    <div class="container-fluid  select2Container">
        <form action="{{ route('return.store') }}" method="post"
              class="card px-3 py-4 border-8">
            @csrf
            <div class="row">
                <div class="form-group col-6">
                    @include('shared.forms.ajaxSelect', [
                        'url' => route('return.filterOrders'),
                        'name' => 'order_id',
                        'className' => 'ajax-user-input getFilteredOrders',
                        'placeholder' => __('Search'),
                        'label' => __('Order'),
                        'minInputLength' => 3,
                    ])
                </div>
                <input type="hidden" name="warehouse_text" value="{{ $defaultWarehouse['text'] ?? '' }}">
                <div class="form-group col-6">
                    @include('shared.forms.ajaxSelect', [
                        'url' => route('pickingCart.filterWarehouses'),
                        'name' => 'warehouse_id',
                        'className' => 'ajax-user-input getFilteredWarehouses',
                        'placeholder' => __('Search'),
                        'dataName' => 'warehouse',
                        'minInputLength' => 0,
                        'label' => __('Warehouse'),
                        'default' => [
                            'id' => old('warehouse_id', $defaultWarehouse['id']),
                            'text' => old('warehouse_text', $defaultWarehouse['text'])
                        ]
                    ])
                </div>
            </div>
            <div class="row">
                <div class="form-group col-3 {{ $errors->has('weight') ? 'has-danger' : '' }}">
                    @include('shared.forms.input', [
                        'label' => __('Weight'),
                        'type' => 'text',
                        'name' => 'weight',
                        'readOnly' => true,
                    ])
                </div>
                <div class="form-group col-3 {{ $errors->has('width') ? ' has-danger' : '' }}">
                    @include('shared.forms.input', [
                        'label' => __('Width'),
                        'type' => 'text',
                        'name' => 'width',
                        'readOnly' => true,
                    ])
                </div>
                <div class="form-group col-3 {{ $errors->has('length') ? ' has-danger' : '' }}">
                    @include('shared.forms.input', [
                        'label' => __('Length'),
                        'type' => 'text',
                        'name' => 'length',
                        'readOnly' => true,
                    ])
                </div>
                <div class="form-group col-3 {{ $errors->has('height') ? ' has-danger' : '' }}">
                    @include('shared.forms.input', [
                        'label' => __('Height'),
                        'type' => 'text',
                        'name' => 'height',
                        'readOnly' => true,
                    ])
                </div>
            </div>
            @if(!empty(! empty($errors->first('items'))))
                <span class="text-danger form-error-messages font-weight-600 font-xs">&nbsp;&nbsp;&nbsp;{{ $errors->first('items') }}</span>
            @endif
            <div class="row">
                <div class="px-1 w-100 table-overflow">
                    <table class="table table-normal">
                        <thead>
                        <tr>
                            <th scope="col">{{ __('Image') }}</th>
                            <th scope="col">{{ __('Product') }}</th>
                            <th scope="col">{{ __('Quantity Shipped') }}</th>
                            <th scope="col">{{ __('Quantity') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="order_items_container">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-12 custom-form-checkbox">
                    @include('shared.forms.checkbox', [
                        'name' => '',
                        'containerClass' => 'px-3 mt-2',
                        'label' => ('Customer is providing their own label'),
                        'checked' => false,
                        'value' => true
                    ])
                </div>
            </div>
            @if (isset($status))
                <div class="row">
                    <div class="col-12 select2Container">
                        <input type="hidden" name="status_text" value="">
                        <div class="form-group col-12">
                            @include('shared.forms.ajaxSelect', [
                                'url' => route('return.filterStatuses'),
                                'name' => 'return_status_id',
                                'className' => 'ajax-user-input getFilteredStatuses',
                                'dataName' => 'status',
                                'placeholder' => __('Search'),
                                'label' => __('Return status'),
                                'containerClass' => '',
                                'minInputLength' => 0
                            ])
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-12 mx-2">
                    <div class="form-group mb-0 mx-2 text-left mb-3">
                        <label for=""
                               class="text-neutral-text-gray font-weight-600 font-xs"
                               data-id="notes">{{ __('Reason:') }}</label>
                        <textarea name="reason" class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mx-2">
                    @include('shared.forms.editSelectTag',
                        [
                            'containerClass' => 'form-group mb-0 mx-2 text-left mb-3',
                            'labelClass' => '',
                            'selectClass' => 'select-ajax-tags',
                            'selectId' => '',
                            'label' => __('Tags'),
                            'minimumInputLength' => 3,
                            'default' => []
                        ]
                    )
                </div>
            </div>
            <button class="globalSave p-0 border-0 bg-logoOrange align-items-center" type="submit">
                <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 502 502" style="enable-background:new 0 0 502 502;" xml:space="preserve">
                    <g>
                        <g>
                            <g>
                                <path fill="#FFF" d="M492,0H10C4.477,0,0,4.477,0,10v424c0,2.652,1.054,5.196,2.929,7.071l58,58C62.804,500.946,65.348,502,68,502h424     c5.523,0,10-4.477,10-10V10C502,4.477,497.523,0,492,0z M86,20h330v240H86V20z M194.045,482H158.06v-67.589h35.985V482z      M313.239,482h-99.194v-77.589c0-5.523-4.477-10-10-10H148.06c-5.523,0-10,4.477-10,10V482h-17.925V381h193.104V482z      M381.866,482h-48.627V381h48.627V482z M482,482h-80.134V371c0-5.523-4.477-10-10-10h-68.627H110.134c-5.523,0-10,4.477-10,10     v111H72.142L20,429.858V20h46v250c0,5.523,4.477,10,10,10h350c5.523,0,10-4.477,10-10V20h46V482z"/>
                                <path fill="#FFF" d="M367.5,62H345c-5.523,0-10,4.477-10,10s4.477,10,10,10h22.5c5.523,0,10-4.477,10-10S373.023,62,367.5,62z"/>
                                <path fill="#FFF" d="M134.5,82H299c5.523,0,10-4.477,10-10s-4.477-10-10-10H134.5c-5.523,0-10,4.477-10,10S128.977,82,134.5,82z"/>
                                <path fill="#FFF" d="M367.5,129h-233c-5.523,0-10,4.477-10,10s4.477,10,10,10h233c5.523,0,10-4.477,10-10S373.023,129,367.5,129z"/>
                                <path fill="#FFF" d="M367.5,196h-233c-5.523,0-10,4.477-10,10s4.477,10,10,10h233c5.523,0,10-4.477,10-10S373.023,196,367.5,196z"/>
                            </g>
                        </g>
                    </g>
                    <g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g> </g>
                </svg>
            </button>
        </form>
    </div>
@endsection
@push('js')
    <script>
        new ReturnOrder('{{ $keyword ?? '' }}');
    </script>
@endpush
