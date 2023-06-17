<?php

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\DestroyRequest;
use App\Http\Requests\Supplier\StoreRequest;
use App\Http\Requests\Supplier\UpdateRequest;
use App\Http\Resources\SupplierTableResource;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Supplier::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('supplier.index', [
            'datatableOrder' => app()->editColumn->getDatatableOrder('suppliers'),
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'suppliers.id';
        $sortDirection = 'desc';

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']] ? $tableColumns[$columnOrder[0]['column']]['name'] : 'suppliers.id';
            $sortDirection = $columnOrder[0]['dir'];
        }

        $supplierCollection = Supplier::join ('customers', 'suppliers.customer_id', '=', 'customers.id')
            ->join('contact_informations AS customer_contact_information', 'customers.id', '=', 'customer_contact_information.object_id')
            ->join('contact_informations', 'suppliers.id', '=', 'contact_informations.object_id')
            ->where('customer_contact_information.object_type', Customer::class)
            ->where('contact_informations.object_type', Supplier::class)
            ->select('suppliers.*')
            ->groupBy('suppliers.id')
            ->orderBy($sortColumnName, $sortDirection);

        $term = $request->get('search')['value'];

        $customers = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $supplierCollection = $supplierCollection->whereIn('suppliers.customer_id', $customers);

        if ($term) {
            // TODO: sanitize term
            $term = $term . '%';

            $supplierCollection
                ->whereHas('contactInformation', function($query) use ($term) {
                    $query->where('name', 'like', $term)
                        ->orWhere('address', 'like', $term)
                        ->orWhere('city', 'like', $term)
                        ->orWhere('zip', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('phone', 'like', $term);
                });
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $supplierCollection = $supplierCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $locations = $supplierCollection->get();
        $supplierCollection = SupplierTableResource::collection($locations);

        $visibleFields = app('editColumn')->getVisibleFields('suppliers');

        return response()->json([
            'data' => $supplierCollection,
            'visibleFields' => $visibleFields,
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|Application|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        app()->supplier->store($request);

        return response()->json([
            'success' => true,
            'message' => __('Vendor successfully created.')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Supplier $supplier
     * @return Application|Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', ['supplier' => $supplier->load('products')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Supplier $supplier
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, Supplier $supplier): JsonResponse
    {
        app()->supplier->update($request, $supplier);

        return response()->json([
            'success' => true,
            'message' => __('Vendor successfully updated.')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRequest $request
     * @param Supplier $supplier
     * @return JsonResponse
     */
    public function destroy(DestroyRequest $request, Supplier $supplier): JsonResponse
    {
        app()->supplier->destroy($request, $supplier);

        return response()->json([
            'success' => true,
            'message' => __('Vendor successfully deleted')
        ]);
    }

    public function filterCustomers(Request $request)
    {
        return app()->supplier->filterCustomers($request);
    }

    public function filterProducts(Request $request, Customer $customer)
    {
        return app()->supplier->filterProducts($request, $customer);
    }

    public function filterByProduct(Request $request, Product $product)
    {
        $results = [];
        $suppliers = app()->supplier->filterByProduct($request, $product);

        if ($suppliers->count()) {
            foreach ($suppliers as $supplier) {
                $results[] = [
                    'id' => $supplier->id,
                    'text' => $supplier->contactInformation->name . ', ' . $supplier->contactInformation->email . ', ' . $supplier->contactInformation->zip . ', ' . $supplier->contactInformation->city . ', ' . $supplier->contactInformation->phone
                ];
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    public function getVendorModal(Supplier $supplier): \Illuminate\Contracts\View\View
    {
        return View::make('shared.modals.components.vendor.edit', compact('supplier'));
    }
}
