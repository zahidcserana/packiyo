@php
    if (!function_exists('getBarcodeBase64Src')) {
        function getBarcodeBase64Src($number): string {
            return 'data:image/png;base64,' . base64_encode(
                app(Picqer\Barcode\BarcodeGeneratorPNG::class)->getBarcode(
                        sprintf("%011d", $number),
                        \Picqer\Barcode\BarcodeGenerator::TYPE_CODE_128
                )
            );
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ sprintf("%011d", $bulkShipBatch->id) }}_bulk_ship.pdf</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <style media="screen">
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
                margin: 33pt 33pt 50pt;
            }

            p {
                margin-top: 0;
                margin-bottom: 1rem;
            }

            table td,
            table th {
                padding-right: 0.5rem;
            }

            table th {
                text-align: left;
            }

        </style>
    </head>

    <body>
        <div>
            <p style="margin-top: 1rem;">Batch ID: {{ $bulkShipBatch->id }}</p>
            <p>Orders shipped: {{ $bulkShipBatch->orders->count() }}</p>
            <p>Created at: {{ $bulkShipBatch->created_at->format('d/m/Y H:i') }}</p>
            <p>Box: {{ Arr::get($barcodeRows, 'box.name') }}</p>
            <p>Dimensions: {{ Arr::get($barcodeRows, 'box.size') }}</p>
            <p>Picker: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</p>
            <p>Packer: _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</p>

            <table style="margin-top: 2rem;">
                <thead>
                    <th>Barcode</th>
                    <th>Location</th>
                    <th>Pick</th>
                </thead>
                <tbody>
                    @foreach($barcodeRows['ids'] ?? [] as $barcodeRowLocation => $barcodeRowProducts)
                        @foreach($barcodeRowProducts as $barcodeRowProduct => $toPick)
                            <tr>
                                <td>
                                    <img style="height: 14pt;" src="{{ getBarcodeBase64Src(Arr::get($barcodeRows, "names.products.$barcodeRowProduct.barcode")) }}" />
                                </td>
                                <td>
                                    <img style="height: 14pt;" src="{{ getBarcodeBase64Src(Arr::get($barcodeRows, "names.products.$barcodeRowLocation.barcode")) }}" />
                                </td>
                                <td>x {{ $toPick }}</td>
                            </tr>
                            <tr>
                                <td>{{ Arr::get($barcodeRows, "names.products.$barcodeRowProduct.name") }}</td>
                                <td>{{ Arr::get($barcodeRows, "names.locations.$barcodeRowLocation.name") }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>
