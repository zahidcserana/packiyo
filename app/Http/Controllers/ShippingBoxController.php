<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingBox\DestroyRequest;
use App\Http\Requests\ShippingBox\StoreRequest;
use App\Http\Requests\ShippingBox\UpdateRequest;
use App\Http\Resources\ShippingBoxTableResource;
use App\Models\ContactInformation;
use App\Models\Customer;
use App\Models\ShippingBox;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class ShippingBoxController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ShippingBox::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('shipping_box.index', [
            'page' => 'shipping_boxes',
            'datatableOrder' => app()->editColumn->getDatatableOrder('shipping-box'),
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $customer = app()->user->getSelectedCustomers();

        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'shipping_boxes.name';
        $sortDirection = 'asc';
        $term = $request->get('search')['value'];

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $shippingBoxQuery = ShippingBox::query();

        if ($customer) {
            $customers = $customer->pluck('id')->toArray();

            $shippingBoxQuery = $shippingBoxQuery->whereIn('customer_id', $customers);
        }

        if ($term) {
            $term = $term . '%';
            $shippingBoxQuery->where('name', 'like', $term);
        }

        $shippingBoxQuery->orderBy($sortColumnName, $sortDirection);

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $shippingBoxQuery = $shippingBoxQuery->skip($request->get('start'))->limit($request->get('length'));
        }

        $shippingBoxes = $shippingBoxQuery->get();

        $shippingBoxCollection = ShippingBoxTableResource::collection($shippingBoxes);
        return response()->json([
            'data' => $shippingBoxCollection,
            'visibleFields' => app()->editColumn->getVisibleFields('shipping-box'),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('shipping_box.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return Response
     */
    public function store(StoreRequest $request)
    {
        app()->shippingBox->store($request);

        return redirect()->route('shipping_box.index')->withStatus(__('Shipping box successfully created.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param ShippingBox $shippingBox
     * @return Factory|\Illuminate\View\View
     */
    public function edit(ShippingBox $shippingBox)
    {
        return view('shipping_box.edit', ['shippingBox' => $shippingBox]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param ShippingBox $shippingBox
     * @return Response
     */
    public function update(UpdateRequest $request, ShippingBox $shippingBox)
    {
        app()->shippingBox->update($request, $shippingBox);

        return redirect()->back()->withStatus(__('Shipping box successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRequest $request
     * @param ShippingBox $shippingBox
     * @return Response
     */
    public function destroy(DestroyRequest $request ,ShippingBox $shippingBox)
    {
        app()->shippingBox->destroy($request, $shippingBox);

        return redirect()->back()->withStatus(__('Shipping box successfully deleted.'));
    }

    public function filterCustomers(Request $request): JsonResponse
    {
        $term = $request->get('term');
        $results = [];

        if ($term) {
            $contactInformation = Customer::whereHas('contactInformation', static function ($query) use ($term) {
                $query->where('name', 'like', $term . '%' )
                    ->orWhere('company_name', 'like',$term . '%')
                    ->orWhere('email', 'like',  $term . '%' )
                    ->orWhere('zip', 'like', $term . '%' )
                    ->orWhere('city', 'like', $term . '%' )
                    ->orWhere('phone', 'like', $term . '%' );
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
}
