@props([
    'tableId' => null,
    'searchPlaceholder' => null,
    'search' => true,
    'filters' => 'global',
    'columns' => true,
    'tableActions' => null,
    'tableClass' => null,
    'searchClass' => null,
    'containerClass' => null,
    'filterMenu' => null,
    'data' => [],
    'datatableOrder' => [],
    'bulkEdit' => null,
    'showTotalRecords' => false,
    'countRecordsUrl' => null,
    'disableAutoload' => false,
    'disableAutoloadText' => __('Use search or filters to get the data.'),
    'disableAutoloadButtonLabel' => __('Load page'),
    'disableAutoloadAllowLoadButton' => (bool) customer_settings(app('user')->getSessionCustomer()->id ?? null, \App\Models\CustomerSetting::CUSTOMER_SETTING_DISABLE_AUTOLOAD_ALLOW_LOAD_BUTTON, 0),
    'widgetUrl' => null
])
<div class="container-fluid {{ $containerClass }}" id="{{ $tableId }}-container">
    @isset($widgetUrl)
        <div class="widget-card" data-widget-url="{{ $widgetUrl }}"></div>
    @endisset
    <div class="row">
        @if ($search || $filters || $columns)
            <div class="col-12">
                <div class="row border-12 py-0 p-3 py-md-3 m-0 mb-3 bg-white actionsBlock justify-content-end">
                    @if ($search)
                        <div class="col-12 col-md-6 p-0 d-flex align-items-center">
                            <div class="form-group mb-0">
                                <div class="input-group input-group-alternative input-group-merge bg-lightGrey font-sm tableSearch">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-lightGrey">
                                            <img src="{{ asset('img/search.svg') }}" alt="">
                                        </span>
                                    </div>

                                    <input
                                        class="form-control font-sm bg-lightGrey font-weight-600 text-neutral-gray searchText px-2 py-0 {{ $searchClass }}"
                                        placeholder="{{ $searchPlaceholder }}"
                                        type="text"
                                    >
                                </div>
                            </div>
                            @if (! empty($filters) && $filters !== 'global')
                                <div class="ml-2">
                                    <div id="filter-icon">
                                        <i class="picon-filter-light icon-lg"></i>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                    @if ($columns)
                        <div class="col-12 col-md-6 d-flex align-items-center justify-content-start justify-content-md-end pt-2 px-0  pt-md-0">
                            @if ($showTotalRecords && !is_null($countRecordsUrl))
                                <div class="d-flex">
                                    <div class="font-xs font-weight-600 total-records-section total-records" data-count-records-url="{{ $countRecordsUrl }}"></div>
                                </div>
                            @endif
                            @if ($tableActions)
                                <div class="d-flex">
                                    {{ $tableActions }}
                                </div>
                            @endif
                            <div class="columns">
                                    <button type="button" class="action-button font-sm font-weight-600" data-toggle="modal" data-target="#{{ $tableId }}-columns">
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
                                                                    'placeholder' => __('Select column'),
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
                                                                    'placeholder' => __('Select direction'),
                                                                    'attributes' => [
                                                                        'data-toggle' => 'native-select',
                                                                    ],
                                                                    'options' => [
                                                                        'asc' => 'Ascending',
                                                                        'desc' => 'Descending',
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
                </div>
            </div>
        @endif

        <div class="col-12">
            <div class="card">
                @if($filterMenu)
                    <div class="row border-12  py-0 py-md-3 p-3 m-0 mb-3 bg-white collapse select2Container" id="toggleFilterForm">
                        <div class="col-12 col-md-12 p-0">
                            <form autocomplete="off">
                                <div class="row">
                                    @include($filterMenu, ['data' => $data ? $data->toArray() : ''])
                                    <div class="col-12 col-md-3 ml-auto align-self-center">
                                        <button type="submit" class="btn bg-logoOrange text-white mr-4" id="submit-filter-button">{{ __('Filter') }}</button>
                                        <button type="reset" class="btn bg-logoOrange text-white">{{ __('Reset') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-12">
            <div class="card global-container">
                <div class="loading-container d-flex justify-content-center align-items-center p-5">
                    <img width="50px" src="{{ asset('img/loading.gif') }}" alt="">
                </div>

                <div class="table-responsive p-4 d-none">
                    <table
                        class="table align-items-center col-12 items-table {{ $tableClass }}"
                        id="{{ $tableId }}"
                        data-datatable-order="{{ $datatableOrder }}"
                        data-disable-autoload="{{ $disableAutoload }}"
                        data-disable-autoload-text="{{ $disableAutoloadText }}"
                        data-disable-autoload-button-label="{{ $disableAutoloadButtonLabel }}"
                        data-disable-autoload-allow-load-button="{{ $disableAutoloadAllowLoadButton }}"
                    >
                        <thead></thead>
                        <tbody style="cursor:pointer"></tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if($bulkEdit)
    @if(isset($bulkEditForm))
        @include($bulkEditForm)
    @else
        @include('components.bulkEdit')
    @endif
@endif

@push('js')
    @once('js')
        <script>
            new BaseIndex()
        </script>
    @endonce
@endpush
