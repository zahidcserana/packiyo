@props([
    'columns' => true,
    'tableActions' => '',
    'tableId' => '',
])

@if ($columns)
    <div class="col-12 col-md-6 d-flex align-items-center justify-content-start justify-content-md-end pt-2 px-0  pt-md-0">
        @if ($tableActions)
            <div class="d-flex">
                {{ $tableActions }}
            </div>
        @endif
        <div class="columns">
            <button class="font-sm font-weight-600" data-toggle="modal" data-target="#{{ $tableId }}-columns">
                {{ __('Edit Columns') }}
            </button>
            
            <div class="columns-modal">
                <div class="modal fade confirm-dialog" id="{{ $tableId }}-columns" data-positioned="true" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-md mx-0" role="document">
                        <div class="modal-content">
                            <div class="modal-header px-0">
                                <div class="mx-4 pb-4 d-flex w-100 border-bottom-gray">
                                    <h6 class="modal-title text-black text-left"
                                        id="modal-title-notification">{{ __('Edit Columns') }}</h6>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                                        <span aria-hidden="true" class="text-black">&times;</span>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="modal-body py-1 mx--2 columns-order">
                                <div class="row">
                                    <div class="col-5 pr-0">
                                        @include('shared.forms.new.select', [
                                            'name' => 'order_column',
                                            'label' => __('Default Sorting'),
                                            'value' => null,
                                            'attributes' => [
                                                'data-toggle' => 'native-select',
                                            ],
                                            'options' => []
                                        ])
                                    </div>
                                    <div class="col-4 pl-0">
                                        @include('shared.forms.new.select', [
                                            'name' => 'order_direction',
                                            'label' => '&nbsp;',
                                            'value' => null,
                                            'attributes' => [
                                                'data-toggle' => 'native-select',
                                            ],
                                            'options' => [
                                                'asc' => 'Asc',
                                                'desc' => 'Desc',
                                            ]
                                        ])
                                    </div>
                                </div>
                            </div>
                            
                            <div class="modal-body text-black py-3 overflow-auto colvis"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif