<?php

namespace App\Http\Controllers;

use App\Http\Resources\PickingCartResource;
use App\Http\Resources\PickingCartTableResource;
use App\Models\Customer;
use App\Models\PickingCart;
use App\Models\Warehouse;
use Carbon\Carbon;
use App\Http\Requests\PickingCart\{DestroyRequest, StoreRequest, UpdateRequest};
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Arr;
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
use Illuminate\View\View;

class PickingCartController extends Controller
{
    /**
     * @return Factory|View
     */
    public function index()
    {
        return view('picking_carts.index', [
            'datatableOrder' => app()->editColumn->getDatatableOrder('picking-cart'),
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'picking_carts.name';
        $sortDirection = 'asc';

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $pickingCartCollection = PickingCart::join('warehouses', 'picking_carts.warehouse_id', '=', 'warehouses.id')
            ->join('contact_informations AS warehouse_contact_information', 'warehouses.id', '=', 'warehouse_contact_information.object_id')
            ->where('warehouse_contact_information.object_type', Warehouse::class)
            ->select('picking_carts.*')
            ->groupBy('picking_carts.id')
            ->orderBy($sortColumnName, $sortDirection);

        if (!empty($request->get('from_date'))) {
            $pickingCartCollection = $pickingCartCollection->where('picking_carts.created_at', '>=', $request->get('from_date'));
        }

        $customer = app()->user->getSelectedCustomers();

        if ($customer) {
            $customers = $customer->pluck('id')->toArray();

            $pickingCartCollection = $pickingCartCollection->whereIn('warehouses.customer_id', $customers);
        }

        $term = $request->get('search')['value'];

        if ($term) {
            $term = $term . '%';

            $pickingCartCollection->where(function($q) use ($term) {
                $q->where('picking_carts.name', 'like', $term)
                    ->orWhereHas('warehouse.contactInformation', function($query) use ($term) {
                        $query->where('name', 'like', $term)
                            ->orWhere('address', 'like', $term)
                            ->orWhere('city', 'like', $term)
                            ->orWhere('zip', 'like', $term)
                            ->orWhere('email', 'like', $term)
                            ->orWhere('phone', 'like', $term);
                    });
            });
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $pickingCartCollection = $pickingCartCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $pickingCarts = $pickingCartCollection->get();

        $pickingCartCollection = PickingCartTableResource::collection($pickingCarts);

        return response()->json([
            'data' => $pickingCartCollection,
            'visibleFields' => app()->editColumn->getVisibleFields('picking-cart'),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    /**
     * Show the form for creating a new cart.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('picking_carts.create');
    }

    /**
     * Store a newly created cart in storage.
     *
     * @param StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        app()->pickingCart->store($request);

        return redirect()->route('picking_carts.index')->with(__('Picking cart successfully created!'));
    }

    /**
     * Display the specified cart.
     *
     * @param PickingCart $pickingCart
     * @return PickingCartResource
     */
    public function show(PickingCart $pickingCart): PickingCartResource
    {
        return new PickingCartResource($pickingCart);
    }

    /**
     * Show the form for editing the specified cart.
     *
     * @param  PickingCart $pickingCart
     * @return Factory|View
     */
    public function edit(PickingCart $pickingCart)
    {
        return view('picking_carts.edit', ['cart' => $pickingCart]);
    }

    /**
     * Update the specified cart in storage.
     *
     * @param UpdateRequest $request
     * @param PickingCart $pickingCart
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, PickingCart $pickingCart): RedirectResponse
    {
        app()->pickingCart->update($request, $pickingCart);

        return redirect()->back()->withStatus(__('Picking cart successfully updated.'));
    }

    /**
     * Remove the specified cart from storage.
     *
     * @param DestroyRequest $request
     * @return RedirectResponse
     */
    public function destroy(DestroyRequest $request): RedirectResponse
    {
        if (app()->pickingCart->destroy($request)) {
            return redirect()->route('picking_carts.index')->withStatus(__('Picking cart successfully deleted.'));
        }

        return redirect()->route('picking_carts.index')->withErrors(__('Unable to delete picking cart.'));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function filterWarehouses(Request $request)
    {
        return app()->pickingCart->filterWarehouses($request);
    }

    /**
     * @param PickingCart $pickingCart
     * @return mixed
     */
    public function barcode(PickingCart $pickingCart)
    {
        return app()->pickingCart->barcode($pickingCart);
    }
}
