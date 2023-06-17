<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentLabel;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    public function label(Shipment $shipment, ShipmentLabel $shipmentLabel)
    {
        return app('shipment')->label($shipment, $shipmentLabel);
    }

    public function void(Request $request, Shipment $shipment)
    {
        $message = '';

        try {
            $response = app('shipping')->void($shipment);

            if ($request->ajax()) {
                return response()->json($response);
            }

            $message = $response['message'];

            if ($response['success']) {
                return redirect()->back()->withStatus($message);
            }
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
        }

        return redirect()->back()->withErrors($message);
    }

    public function getPackingSlip(Shipment $shipment)
    {
        return app('shipment')->getPackingSlip($shipment);
    }
}
