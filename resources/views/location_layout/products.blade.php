@extends('layouts.location_layout')

@section('content')
    <div class="row">
        @foreach($locationProducts as $locationProduct)
            <div class="col py-2 border">
                <a href="{{ route('product.edit', ['product' => $locationProduct]) }}" target="_blank">
                    @if ($locationProduct->product->productImages->first())
                        <img class="mw-100" src="{{ $locationProduct->product->productImages->first()->source }}" /><br />
                    @endif
                    {{ $locationProduct->product->sku }}<br />
                    {{ $locationProduct->product->name }}<br />
                    {{ __('On hand:') }} {{ $locationProduct->quantity_on_hand }}<br />
                    {{ __('Reserved for picking:') }} {{ $locationProduct->quantity_reserved_for_picking }}<br />
                    <img src="data:image/png;base64,{{
                    base64_encode(
                        app(Picqer\Barcode\BarcodeGeneratorPNG::class)
                            ->getBarcode($locationProduct->product->barcode, \Picqer\Barcode\BarcodeGenerator::TYPE_CODE_128)
                    )
                }}"><br />
                    {{ $locationProduct->product->barcode }}
                </a>
            </div>
        @endforeach
    </div>
@endsection
