<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ sprintf("%011d", $shipment->id) }}_packing_slip.pdf</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <style type="text/css" media="screen">
            * {
                line-height: 1.1;
            }

            html {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                line-height: 1.15;
                margin: 0;
            }

            body {
                font-weight: 400;
                line-height: 1.5;
                color: #212529;
                text-align: left;
                background-color: #fff;
                font-size: 9pt;
                margin: 33pt;
                margin-bottom: 50pt;
            }

            h4 {
                margin-bottom: 0.5rem;
                line-height: 1.2;
                margin-top: 0;
                font-weight: 700;
                text-decoration: none;
                vertical-align: baseline;
                font-size: 12pt;
                font-style: normal;
            }

            p {
                margin-top: 0;
                margin-bottom: 1rem;
            }

            strong {
                font-weight: bolder;
            }

            img {
                vertical-align: middle;
                border-style: none;
            }

            footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                color: #000;
                background-color: #fff;
                margin: 0 33pt;
            }

            table {
                border-collapse: collapse;
            }

            th {
                text-align: inherit;
            }

            .table {
                width: 100%;
                margin-bottom: 1rem;
            }

            .table th,
            .table td {
                padding: 5pt;
                vertical-align: middle;
            }

            .table.items-table td {
                border-top: 1px solid #dee2e6;
            }

            .table thead th {
                vertical-align: bottom;
                border-bottom: 2px solid #dee2e6;
            }

            table.info-table td {
                font-weight: 400;
                text-decoration: none;
                vertical-align: baseline;
                font-size: 9pt;
                font-style: normal;
                padding: 0;
            }

            table.items-table th {
                border-top: 1px solid #000;
                border-bottom: 1pt dotted #999;
                font-size: 11pt;
                font-weight: 400;
            }

            table.items-table tr {
                border-bottom: 1pt dotted #999;
            }

            .mt-5 {
                margin-top: 3rem !important;
            }

            .mt-3 {
                margin-top: 1rem !important;
            }

            .pr-0,
            .px-0 {
                padding-right: 0 !important;
            }

            .pl-0,
            .px-0 {
                padding-left: 0 !important;
            }

            .px-1 {
                padding-right: 1rem !important;
                padding-left: 1rem !important;
            }

            .f-8 {
                font-size: 8pt !important;
            }

            .f-10 {
                font-size: 10pt !important;
            }

            .f-11 {
                font-size: 11pt !important;
            }

            .text-right {
                text-align: right !important;
            }

            .text-center {
                text-align: center !important;
            }

            .border-0 {
                border: none !important;
            }

            .logo {
                max-height: 80pt;
                max-width: 140pt;
                margin-right: 20pt;
            }

            .slip-textbox {
                page-break-inside: avoid;
                line-height: 1.15;
            }
        </style>
    </head>

    <body>
        {{-- Add footer to each page --}}
        <footer>
            {!! customer_settings($shipment->order->customer->id, \App\Models\CustomerSetting::CUSTOMER_SETTING_ORDER_SLIP_FOOTER) !!}

            <div class="text-center">
                <img src="data:image/png;base64,{{
                    base64_encode(
                        app(Picqer\Barcode\BarcodeGeneratorPNG::class)
                            ->getBarcode(sprintf("%011d", $shipment->order->id), \Picqer\Barcode\BarcodeGenerator::TYPE_CODE_128)
                    )
                }}">

                <p class="mt-3">{{ __('Order: :number', ['number' => $shipment->order->number]) }}</p>
            </div>
        </footer>

        {{-- Shipping info / logo --}}
        <table class="table">
            <tbody>
                <tr>
                    <td class="border-0" width="70%">
                        <table class="info-table">
                            <tbody>
                                <tr class="slip-title">
                                    <td colspan="2">
                                        <h1>
                                            {{ customer_settings($shipment->order->customer->id, \App\Models\CustomerSetting::CUSTOMER_SETTING_ORDER_SLIP_HEADING) }}
                                        </h1>
                                    </td>
                                </tr>

                                <tr><td>&nbsp;</td></tr>

                                <tr>
                                    <td>{{ __('Order Number:') }}</td>
                                    <td class="px-1">{{ $shipment->order->number }}</td>
                                </tr>

                                <tr>
                                    <td>{{ __('Order date:') }}</td>
                                    <td class="px-1">{{ $shipment->order->created_at->format('d.m.Y') }}</td>
                                </tr>

                                <tr><td>&nbsp;</td></tr>

                                <tr>
                                    <td>{{ __('Ship to:') }}</td>
                                    <td class="f-10 px-1">
                                        {{ $shipment->order->shippingContactInformation->name ?? '' }}<br />
                                        {{ $shipment->order->shippingContactInformation->address ?? '' }}<br />
                                        @if ($shipment->order->shippingContactInformation->address2)
                                            {{ $shipment->order->shippingContactInformation->address2 }}<br />
                                        @endif
                                        {{ $shipment->order->shippingContactInformation->zip ?? '' }} {{ $shipment->order->shippingContactInformation->city ?? '' }}<br />
                                        {{ $shipment->order->shippingContactInformation->country->name ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="border-0 text-right">
                        <img
                            class="logo"
                            @if ($shipment->order->customer->orderSlipLogo)
                                src="{{ Storage::path($shipment->order->customer->orderSlipLogo->filename) }}"
                            @endif
                            alt="logo"
                        >
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Items table --}}
        <table class="table items-table mt-5">
            <thead>
                <tr>
                    <th scope="col">{{ __('Item description') }}</th>
                    <th scope="col">{{ __('Qty') }}</th>
                    @if ($showPricesOnSlip)
                        <th scope="col">{{ __('Unit Price') }}</th>
                        <th scope="col">{{ __('Total Price') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($shipment->shipmentItems ?? [] as $shipmentItem)
                    <tr>
                        <td>
                            <span class="f-8">
                                {{ __('SKU:') }} {{ $shipmentItem->orderItem->sku }}
                                <br>
                            </span>
                            <span class="f-11">
                                {{ $shipmentItem->orderItem->name }}
                            </span>
                        </td>
                        <td style="width: 150pt;">
                            <span class="f-11">
                                {{ $shipmentItem->quantity }}
                            </span>
                        </td>
                        @if ($showPricesOnSlip)
                            <td style="width: 100pt;">
                                <span class="f-11">
                                    {{ number_format($shipmentItem->orderItem->price, 2) }} {{ $currency }}
                                </span>
                            </td>
                            <td style="width: 100pt;">
                                <span class="f-11">
                                    {{ number_format($shipmentItem->orderItem->price * $shipmentItem->quantity, 2) }} {{ $currency }}
                                </span>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if (!empty($shipment->order->gift_note))
            <div class="mt-3 slip-textbox">
                {{ __('Gift note:') }} {!! $shipment->order->gift_note !!}
            </div>
        @endif
        @if (!empty($shipment->order->slip_note))
            <div class="mt-3 slip-textbox">
                {!! $shipment->order->slip_note !!}
            </div>
        @endif

        <div class="mt-3 slip-textbox">
            {!! customer_settings($shipment->order->customer->id, \App\Models\CustomerSetting::CUSTOMER_SETTING_ORDER_SLIP_TEXT) !!}
        </div>
    </body>
</html>
