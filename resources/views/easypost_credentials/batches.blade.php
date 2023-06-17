@extends('layouts.app')

@section('content')
    @component('layouts.headers.auth', [
        'title' => __('Easypost credentials'),
        'subtitle' => __('Batches')
    ])
    @endcomponent
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="text-right p-3">
                        @if(!Cache::has('easypost_batch_shipments_' . $easypostCredential->id))
                            <a href="{{ route('customers.easypost_credentials.batch_shipments', ['customer' => $customer, 'easypost_credential' => $easypostCredential]) }}" class="btn bg-logoOrange text-white">{{ __('Batch shipments') }}</a>
                        @endif
                        @if(!Cache::has('easypost_scanform_batches_' . $easypostCredential->id))
                            <a href="{{ route('customers.easypost_credentials.scanform_batches', ['customer' => $customer, 'easypost_credential' => $easypostCredential]) }}" class="btn bg-logoOrange text-white">{{ __('Make scanforms') }}</a>
                        @endif
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ __('Carrier') }}</th>
                            <th>{{ __('ID') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('State') }}</th>
                            <th>{{ __('Shipments') }}</th>
                            <th>{{ __('Scan form') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($carrierBatches as $carrierBatch)
                            @foreach($carrierBatch['batches'] as $batch)
                                <tr>
                                    <td>{{ $carrierBatch['carrier']->name }}</td>
                                    <td>{{ $batch['id'] }}</td>
                                    <td>{{ $batch['created_at'] }}</td>
                                    <td>{{ $batch['state'] }}</td>
                                    <td>{{ count($batch['shipments']) }}</td>
                                    <td>
                                        @if($formUrl = \Illuminate\Support\Arr::get($batch, 'scan_form.form_url'))
                                            <a href="{{ $formUrl }}" target="_blank">{{ $batch['scan_form']['id'] }}</a>
                                        @else
                                            {{ json_encode($batch['scan_form']) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
