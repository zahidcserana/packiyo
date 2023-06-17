@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth', [ 'title' => '<span id="search_result_num"></span>'.__(' Search Results')])
    @endcomponent
    <div class="container-fluid">
        <div class="row">

            <div class="col-12">
                <div class="card">

                    <div class="table-responsive p-4">

                        <div class="row w-100">
                            <div class="col-6"><h2>{{__('Orders')}}</h2></div>
                            <div class="col-6 text-right"><a href="{{route('order.search', ['keyword'=>$keyword])}}" class="text-logoOrange" id="view_more_orders"><u>{{__('Show all')}} <span id="show_all_orders"></span> {{__('orders')}}</u></a></div>
                        </div>

                        <table id="search-orders-table" class="table align-items-center col-12 items-table">
                            <thead></thead>
                            <tbody style="cursor:pointer"></tbody>
                        </table>
                    </div>

                    <div class="table-responsive p-4">

                        <div class="row w-100">
                            <div class="col-6"><h2>{{__('Returns')}}</h2></div>
                            <div class="col-6 text-right"><a href="{{route('return.search', ['keyword'=>$keyword])}}" class="text-logoOrange" id="view_more_returns"><u>{{__('Show all')}} <span id="show_all_returns"></span> {{__('returns')}}</u></a></div>
                        </div>

                        <table id="search-returns-table" class="table align-items-center col-12 items-table">
                            <thead></thead>
                            <tbody style="cursor:pointer"></tbody>
                        </table>
                    </div>

                    <div class="table-responsive p-4">

                        <div class="row w-100">
                            <div class="col-6"><h2>{{__('Purchase Orders')}}</h2></div>
                            <div class="col-6 text-right"><a href="{{route('purchase_orders.search', ['keyword'=>$keyword])}}" class="text-logoOrange" id="view_more_purchase_orders"><u>{{__('Show all')}} <span id="show_all_purchase_orders"></span> {{__('purchase Orders')}}</u></a></div>
                        </div>

                        <table id="search-purchase-orders-table" class="table align-items-center col-12 items-table">
                            <thead></thead>
                            <tbody style="cursor:pointer"></tbody>
                        </table>
                    </div>

                    <div class="table-responsive p-4">

                        <div class="row w-100">
                            <div class="col-6"><h2>{{__('Inventory')}}</h2></div>
                            <div class="col-6 text-right"><a href="{{route('product.search', ['keyword'=>$keyword])}}" class="text-logoOrange" id="view_more_inventory"><u>{{__('Show all')}} <span id="show_all_inventory"></span> {{__('products')}}</u></a></div>
                        </div>

                        <table id="search-products-table" class="table align-items-center col-12 items-table">
                            <thead></thead>
                            <tbody style="cursor:pointer"></tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection

@push('js')
    <script>
        new GlobalSearch('{{$keyword}}');
    </script>
@endpush

