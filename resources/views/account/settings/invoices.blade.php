<div class="p-4" id="invoice-tab">
    <div class="row mb-4">
        <div class="col-12 font-weight-600 font-md">
            {{ __('Invoices') }}
        </div>
    </div>
    <div class="row">
        <div class="form-control-label text-neutral-text-gray font-weight-600 font-md">
            {{ __('Here are the Invoices for your Packiyo subscription. If you have any questions, contact') }}
                <span class="text-underline text-black font-weight-600 pl-2"> {{ __('Packiyo Customer Support.') }}</span>
        </div>
    </div>
    <br><br>
    <div class="row">
        <div class="table-responsive overflow-scroll-x items-table">
            <table class="col-12 table align-items-center table-flush">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Invoice date') }}</th>
                        <th scope="col">{{ __('Number') }}</th>
                        <th scope="col">{{ __('Amount') }}</th>
                        <th scope="col">{{ __('Due date') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->date()->toFormattedDateString() }}</td>
                        <td><a href="{{ route('account.download-invoice', ['customer' => $customer, 'invoice' => $invoice->id]) }}">{{ $invoice->asStripeInvoice()->number }}</a></td>
                        <td>{{ $invoice->amountDue() }}</td>
                        <td>{{ $invoice->dueDate()->toFormattedDateString() }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @if($invoices->isEmpty())
                <br>
                <div class="d-flex col-12 justify-content-center">
                    {{ __('You don\'t have any invoices yet!') }}
                </div>
            @endif
        </div>
    </div>
</div>

