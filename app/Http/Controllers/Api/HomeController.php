<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(Order::class);
        $this->authorizeResource(Product::class);
    }

    public function statistics(Request $request)
    {
        return response()->json((app()->home->statistics($request)));
    }
}
