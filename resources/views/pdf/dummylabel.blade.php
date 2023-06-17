<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>
</head>
<body>
<strong>{{ __('Sender:') }}</strong><br />
{{ $senderCustomerContactInformation->name ?? '' }}<br />
{{ $senderContactInformation->phone ?? '' }}<br />
{{ $senderContactInformation->email ?? '' }}<br />
{{ $senderContactInformation->address ?? '' }}<br />
@if(!empty($senderContactInformation->address2 ?? ''))
    {{ $senderContactInformation->address2 ?? '' }}<br />
@endif
{{ $senderContactInformation->city ?? '' }} {{ $senderContactInformation->state ?? '' }} {{ $senderContactInformation->zip ?? '' }}<br />

{{ $senderContactInformation->country->name ?? '' }}<br />
<br />
<strong>{{ __('Receiver:') }}</strong><br />
{{ $receiverCustomerContactInformation->name ?? '' }}<br />
{{ $receiverContactInformation->company_name ?? '' }}<br />
{{ $receiverContactInformation->phone ?? '' }}<br />
{{ $receiverContactInformation->email ?? '' }}<br />
{{ $receiverContactInformation->address ?? '' }}<br />
@if(!empty($receiverContactInformation->address2 ?? ''))
    {{ $receiverContactInformation->address2 ?? '' }}<br />
@endif
{{ $receiverContactInformation->city ?? '' }} {{ $receiverContactInformation->state ?? '' }} {{ $receiverContactInformation->zip ?? '' }}<br />
{{ $receiverContactInformation->country->name ?? '' }}<br />
<br />
<div style="text-align: center;">
    <img src="data:image/png;base64,{{ base64_encode($barcode) }}" alt="barcode">
    <p>{{ $barcodeNumber }}</p>
</div>
</body>
</html>
