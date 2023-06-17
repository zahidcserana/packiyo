@extends('layouts.location_layout')

@section('content')
    <div class="row">
        @foreach($locations as $location)
            <div class="col py-2 border">
                <a href="{{ route('location_layout.product.index', ['location' => $location]) }}">
                    {{ $location->name }}<br />
                    {{ __('Total products: ') }} {{ $location->products->count() }}<br />
                    {{ __('Total on hand: ') }} {{ $location->products->sum('quantity_on_hand') }}<br />
                    {{ __('Total reserved for picking: ') }} {{ $location->products->sum('quantity_reserved_for_picking') }}<br />
                    <img src="data:image/png;base64,{{
                    base64_encode(
                        app(Picqer\Barcode\BarcodeGeneratorPNG::class)
                            ->getBarcode($location->barcode, \Picqer\Barcode\BarcodeGenerator::TYPE_CODE_128)
                    )
                }}"><br />
                    {{ $location->barcode }}
                </a>
            </div>
        @endforeach
    </div>
@endsection
