<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationType\{DestroyRequest, StoreRequest, UpdateRequest};
use App\Http\Resources\LocationTypeTableResource;
use App\Models\{Customer, LocationType};
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};

class LocationTypeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(LocationType::class);
    }

    public function index()
    {
        return view('location_types.index', [
            'page' => 'locations',
            'datatableOrder' => app()->editColumn->getDatatableOrder('location-types'),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'location_types.name';
        $sortDirection = 'asc';
        $term = $request->get('search')['value'];

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $locationTypesCollection = LocationType::join('customers', 'location_types.customer_id', '=', 'customers.id')
            ->join('contact_informations AS customer_contact_information', 'customers.id', '=', 'customer_contact_information.object_id')
            ->where('customer_contact_information.object_type', Customer::class)
            ->groupBy('location_types.id')
            ->select('location_types.*')
            ->orderBy($sortColumnName, $sortDirection);

        $customer = app()->user->getSelectedCustomers();

        if ($customer) {
            $customers = $customer->pluck('id')->toArray();

            $locationTypesCollection = $locationTypesCollection->whereIn('location_types.customer_id', $customers);
        }

        if ($term) {
            $term = $term . '%';

            $locationTypesCollection->where(function ($q) use ($term) {
                $q->where('location_types.name', 'like', $term)
                    ->orWhereHas('customer.contactInformation', function ($q) use ($term) {
                        $q->where('name', 'like', $term);
                    });
            });
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $locationTypesCollection = $locationTypesCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $locationTypes = $locationTypesCollection->get();
        $locationTypesCollection = LocationTypeTableResource::collection($locationTypes);

        return response()->json([
            'data' => $locationTypesCollection,
            'visibleFields' => app()->editColumn->getVisibleFields('location-types'),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    public function create()
    {
        return view('location_types.create');
    }

    /**
     * @param StoreRequest $request
     * @return mixed
     */
    public function store(StoreRequest $request)
    {
        app()->locationType->store($request);

        return redirect()->route('location_type.index')->withStatus(__('Location type was successfully added.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LocationType $locationType
     * @return Factory|View
     */
    public function edit(LocationType $locationType)
    {
        return view('location_types.edit', compact('locationType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param LocationType $locationType
     * @return RedirectResponse
     */
    public function update(UpdateRequest $request, LocationType $locationType): RedirectResponse
    {
        app()->locationType->update($request, $locationType);

        return redirect()->route('location_type.index')->withStatus(__('Location type successfully updated.'));
    }

    /**
     * @param DestroyRequest $request
     * @param LocationType $location_type
     * @return mixed
     */
    public function destroy(DestroyRequest $request, LocationType $location_type)
    {
        app()->locationType->destroy($request, $location_type);

        return redirect()->back()->withStatus(__('Location type was successfully deleted.'));
    }

    /**
     * @param Request $request
     * @param Customer|null $customer
     * @return mixed
     */
    public function getTypes(Request $request, Customer $customer = null)
    {
        return app()->locationType->getTypes($request, $customer);
    }
}
