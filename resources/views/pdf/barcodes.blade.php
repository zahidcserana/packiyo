<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>
    </head>
    <style>
        @page {
            margin: 8px;
        }
        body {
            margin: 0;
            text-align: center;
        }
        p {
            padding: 0;
            margin: 0;
        }
    </style>
    <body>
        @foreach ($barcodes as $barcode)
            <div style="{{ !$loop->last ? 'page-break-after: always;' : '' }}">
                <p>{{ Arr::get($barcode, 'name') }}</p>
                <img src="data:image/png;base64,{{ base64_encode(Arr::get($barcode, 'barcode')) }}" alt="barcode">
                <p>{{ Arr::get($barcode, 'number') }}</p>
            </div>
        @endforeach
    </body>
</html>
