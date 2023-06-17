@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth')
    @endcomponent
    <div class="container-fluid mt--6">
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Purchase Order Statuses') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('purchase_order_status.create') }}" class="btn btn-sm btn-primary">{{ __('Add order status') }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive py-4">
                        <table id="purchase-order-status-table" class="table align-items-center table-flush datatable-basic table-hover">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">{{ __('Name') }}</th>
                                <th scope="col">{{ __('Customer') }}</th>
                                <th scope="col" class="text-center">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody style="cursor:pointer">
                            @foreach ($purchaseOrderStatuses as $purchaseOrderStatus)
                                <tr>
                                    <td>{{ $purchaseOrderStatus->name }}</td>
                                    <td>
                                        <a href="/customer/{{ $purchaseOrderStatus->customer->id }}/edit">{{ $purchaseOrderStatus->customer->contactInformation->name }}</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('purchase_order_status.edit', [ 'purchase_order_status' => $purchaseOrderStatus ]) }}" class="btn btn-primary edit">{{ __('Edit') }}</a>
                                        <form action="{{ route('purchase_order_status.destroy', ['purchase_order_status' => $purchaseOrderStatus, 'id' => $purchaseOrderStatus->id]) }}" method="post" style="display: inline-block">
                                            @csrf
                                            @method('delete')
                                            <button type="button" class="btn btn-danger" data-confirm-message="{{ __('Are you sure you want to delete this purchase order status?') }}">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        new PurchaseOrderStatus();
    </script>
@endpush

