<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Location;
use App\Models\Warehouse;

class LocationLayoutController extends Controller
{
    public function customerIndex()
    {
        return view('location_layout.customers', [
            'customers' => app('user')->getCustomers()
        ]);
    }

    public function warehouseIndex(Customer $customer)
    {
        return view('location_layout.warehouses', [
            'warehouses' => $customer->warehouses->sortBy('contactInformation.name')
        ]);
    }

    public function locationIndex(Warehouse $warehouse)
    {
        $this->authorize('view', $warehouse);

        return view('location_layout.locations', [
            'locations' => $warehouse->locations()->orderBy('name')->get()
        ]);
    }

    public function productIndex(Location $location)
    {
        $this->authorize('view', $location);

        return view('location_layout.products', [
            'locationProducts' => $location->products()->orderBy('quantity_on_hand', 'desc')->get()
        ]);
    }
}
