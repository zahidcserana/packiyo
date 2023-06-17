<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingMethod\DropPointRequest;
use App\Http\Requests\ShippingMethod\UpdateRequest;
use App\Http\Resources\ShippingMethodTableResource;
use App\Models\ShippingMethod;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Request;

class ShippingMethodController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ShippingMethod::class);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('shipping_methods.index', [
            'datatableOrder' => app()->editColumn->getDatatableOrder('shipping_method'),
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'shipping_methods.name';
        $sortDirection = 'asc';

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $customerIds = app()->user->getSelectedCustomers()->pluck('id')->toArray();

        $shippingMethodCollection = ShippingMethod
            ::whereHas('shippingCarrier', function ($query) use ($customerIds) {
                $query->whereIn('customer_id', $customerIds);
            })
            ->orderBy($sortColumnName, $sortDirection);

        $term = $request->get('search')['value'];

        if ($term) {
            $term = $term . '%';

            $shippingMethodCollection->where(function ($q) use ($term) {
                $q->whereHas('shippingCarrier', function ($query) use ($term) {
                    $query->where('name', 'like', $term);
                });
            });
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $shippingMethodCollection = $shippingMethodCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $shippingMethods = $shippingMethodCollection->get();

        $visibleFields = app()->editColumn->getVisibleFields('shipping_methods');

        return response()->json([
            'data' => ShippingMethodTableResource::collection($shippingMethods),
            'visibleFields' => $visibleFields,
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingMethod $shippingMethod)
    {
        return view('shipping_methods.edit', compact('shippingMethod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, ShippingMethod $shippingMethod)
    {
        app('shippingMethod')->update($request, $shippingMethod);

        return redirect()->back()->withStatus(__('Shipping method successfully updated.'));
    }

    /**
     * @param DropPointRequest $request
     * @return mixed
     */
    public function getDropPoints(DropPointRequest $request)
    {
        return app('webshipperShipping')->getDropPoints($request);
    }
}
