<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ sprintf("%011d", $order->id) }}_order_slip.pdf</title>
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
            {!! customer_settings($order->customer->id, \App\Models\CustomerSetting::CUSTOMER_SETTING_ORDER_SLIP_FOOTER) !!}

            <div class="text-center">
                <img src="data:image/png;base64,{{
                    base64_encode(
                        app(Picqer\Barcode\BarcodeGeneratorPNG::class)
                            ->getBarcode(sprintf("%011d", $order->id), \Picqer\Barcode\BarcodeGenerator::TYPE_CODE_128)
                    )
                }}">

                <p class="mt-3">{{ __('Order: :number', ['number' => $order->number]) }}</p>
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
                                            {{ customer_settings($order->customer->id, \App\Models\CustomerSetting::CUSTOMER_SETTING_ORDER_SLIP_HEADING) }}
                                        </h1>
                                    </td>
                                </tr>

                                <tr><td>&nbsp;</td></tr>

                                <tr>
                                    <td>{{ __('Order Number:') }}</td>
                                    <td class="px-1">{{ $order->number }}</td>
                                </tr>

                                <tr>
                                    <td>{{ __('Order date:') }}</td>
                                    <td class="px-1">{{ $order->created_at->format('d.m.Y') }}</td>
                                </tr>

                                <tr><td>&nbsp;</td></tr>

                                <tr>
                                    <td>{{ __('Ship to:') }}</td>
                                    <td class="f-10 px-1">
                                        {{ $order->shippingContactInformation->name ?? '' }}<br />
                                        {{ $order->shippingContactInformation->address ?? '' }}<br />
                                        @if ($order->shippingContactInformation->address2)
                                            {{ $order->shippingContactInformation->address2 }}<br />
                                        @endif
                                        {{ $order->shippingContactInformation->zip ?? '' }} {{ $order->shippingContactInformation->city ?? '' }}<br />
                                        {{ $order->shippingContactInformation->country->name ?? '' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td class="border-0 text-right">
                        <img
                            class="logo"
                            @if ($order->customer->orderSlipLogo)
                                src="{{ Storage::path($order->customer->orderSlipLogo->filename) }}"
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
                @foreach($order->orderItemsGreaterThanZero() ?? [] as $item)
                    <tr>
                        <td>
                            <span class="f-8">
                                {{ __('SKU:') }} {{ $item->sku }}
                                <br>
                            </span>
                            <span class="f-11">
                                {{ $item->name }}
                            </span>
                        </td>
                        <td style="width: 150pt;">
                            <span class="f-11">
                                {{ $item->quantity }}
                            </span>
                        </td>
                        @if ($showPricesOnSlip)
                            <td style="width: 100pt;">
                                <span class="f-11">
                                    {{ number_format($item->price, 2) }} {{ $currency }}
                                </span>
                            </td>
                            <td style="width: 100pt;">
                                <span class="f-11">
                                    {{ number_format($item->price * $item->quantity, 2) }} {{ $currency }}
                                </span>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if (!empty($order->gift_note))
            <div class="mt-3 slip-textbox">
                {{ __('Gift note:') }} {!! $order->gift_note !!}
            </div>
        @endif
        @if (!empty($order->slip_note))
            <div class="mt-3 slip-textbox">
                {!! $order->slip_note !!}
            </div>
        @endif

        <div class="mt-3 slip-textbox">
            {!! customer_settings($order->customer->id, \App\Models\CustomerSetting::CUSTOMER_SETTING_ORDER_SLIP_TEXT) !!}
        </div>
    </body>
</html>
