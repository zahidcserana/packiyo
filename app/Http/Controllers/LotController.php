<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Requests\Lot\{DestroyRequest, StoreRequest, UpdateRequest};
use App\Http\Resources\LotTableResource;
use App\Models\{Customer, Lot, OrderItem};
use Illuminate\Support\Arr;
use Illuminate\Http\{JsonResponse, Request};

class LotController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Lot::class);
    }

    public function index()
    {
        return view('lot.index', [
            'page' => 'lots',
            'datatableOrder' => app()->editColumn->getDatatableOrder('lot'),
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'lots.created_at';
        $sortDirection = 'desc';

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $term = $request->get('search')['value'];

        $lotCollection = Lot::join('products', 'lots.product_id', '=', 'products.id')
            ->join('customers', 'lots.customer_id', '=', 'customers.id')
            ->join('contact_informations AS customer_contact_information', 'customers.id', '=', 'customer_contact_information.object_id')
            ->where('customer_contact_information.object_type', Customer::class)
            ->groupBy('lots.id')
            ->select('lots.*')
            ->orderBy($sortColumnName, $sortDirection);

        $customer = app()->user->getSelectedCustomers();

        if ($customer) {
            $customers = $customer->pluck('id')->toArray();
            $lotCollection = $lotCollection->whereIn('lots.customer_id', $customers);
        }

        if ($term) {
            $term = $term . '%';
            $lotCollection->where(function ($q) use ($term) {
                $q->where('lots.name', 'like', $term)
                    ->orWhereHas('customer.contactInformation', function ($query) use ($term) {
                        $query->where('name', 'like', $term);
                    })
                    ->orWhere('products.name', 'like', $term)
                    ->orWhere('products.sku', 'like', $term);
            });
        }

        if ($request->get('length') && ((int)$request->get('length')) !== -1) {
            $lotCollection = $lotCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $lots = $lotCollection->get();

        return response()->json([
            'data' => LotTableResource::collection($lots),
            'visibleFields' => app()->editColumn->getVisibleFields('lot'),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    public function create()
    {
        return view('lot.createEdit', ['lot' => null]);
    }

    public function store(StoreRequest $request)
    {
        app()->lot->store($request);

        return response()->json([
            'success' => true,
            'message' => __('Lot successfully created.')
        ]);
    }

    public function show(Lot $lot)
    {

    }

    public function edit(Lot $lot)
    {
        return view('lot.createEdit', ['lot' => $lot]);
    }

    public function update(UpdateRequest $request, Lot $lot)
    {
        app()->lot->update($request, $lot);

        return response()->json([
            'success' => true,
            'message' => __('Lot successfully updated.')
        ]);
    }

    public function destroy(DestroyRequest $request, Lot $lot)
    {
        app()->lot->destroy($request, $lot);

        return back()->withStatus('Lot successfully deleted.');
    }

    public function filterCustomers(Request $request): JsonResponse
    {
        $term = $request->get('term');
        $results = [];

        if ($term) {
            $contactInformation = Customer::whereHas('contactInformation', static function ($query) use ($term) {
                $query->where('name', 'like', $term . '%')
                    ->orWhere('company_name', 'like', $term . '%')
                    ->orWhere('email', 'like', $term . '%')
                    ->orWhere('zip', 'like', $term . '%')
                    ->orWhere('city', 'like', $term . '%')
                    ->orWhere('phone', 'like', $term . '%');
            })
                ->get();

            foreach ($contactInformation as $information) {
                $results[] = [
                    'id' => $information->id,
                    'text' => $information->contactInformation->name
                ];
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }

    public function filterLots(Request $request)
    {
        $lots = app()->lot->filterLots($request);
        $results = [];
        if (!is_null($lots)) {
            foreach ($lots as $lot) {
                $results[] = [
                    'id' => $lot->id,
                    'text' => $lot->nameAndExpirationDateAndSupplierName
                ];
            }
        }
        return response()->json([
            'results' => $results
        ]);
    }
}
