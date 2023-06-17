<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseOrderStatus\DestroyRequest;
use App\Http\Requests\PurchaseOrderStatus\StoreRequest;
use App\Http\Requests\PurchaseOrderStatus\UpdateRequest;
use App\Models\Customer;
use App\Models\PurchaseOrderStatus;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PurchaseOrderStatusController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(PurchaseOrderStatus::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $purchaseOrderStatusQuery = PurchaseOrderStatus::query();

        $customers = app()->user->getSelectedCustomers()->pluck('id')->toArray();

        $purchaseOrderStatusQuery = $purchaseOrderStatusQuery->whereIn('customer_id', $customers);

        return view('purchase_order_status.index', ['purchaseOrderStatuses' => $purchaseOrderStatusQuery->get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('purchase_order_status.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return Response
     */
    public function store(StoreRequest $request)
    {
        app()->purchaseOrderStatus->store($request);

        return redirect()->route('purchase_order_status.index')->withStatus(__('Purchase Order status successfully created.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param PurchaseOrderStatus $purchaseOrderStatus
     * @return Application|Factory|View
     */
    public function edit(PurchaseOrderStatus $purchaseOrderStatus)
    {
        return view('purchase_order_status.edit', ['purchaseOrderStatus' => $purchaseOrderStatus]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param PurchaseOrderStatus $purchaseOrderStatus
     * @return Response
     */
    public function update(UpdateRequest $request, PurchaseOrderStatus $purchaseOrderStatus)
    {
        app()->purchaseOrderStatus->update($request, $purchaseOrderStatus);

        return redirect()->back()->withStatus(__('Purchase Order status successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRequest $request
     * @param PurchaseOrderStatus $purchaseOrderStatus
     * @return Response
     */
    public function destroy(DestroyRequest $request ,PurchaseOrderStatus $purchaseOrderStatus)
    {
        app()->purchaseOrderStatus->destroy($request, $purchaseOrderStatus);

        return redirect()->back()->withStatus(__('Purchase Order status successfully deleted.'));
    }

    public function filterCustomers(Request $request): JsonResponse
    {
        $term = $request->get('term');
        $results = [];

        if ($term) {
            $contactInformation = Customer::whereHas('contactInformation', function($query) use ($term) {
                $query->where('name', 'like', $term . '%' )
                    ->orWhere('company_name', 'like',$term . '%')
                    ->orWhere('email', 'like',  $term . '%' )
                    ->orWhere('zip', 'like', $term . '%' )
                    ->orWhere('city', 'like', $term . '%' )
                    ->orWhere('phone', 'like', $term . '%' );
            })->get();

            foreach ($contactInformation as $information) {
                if ($information->count()) {
                    $results[] = [
                        'id' => $information->id,
                        'text' => $information->contactInformation->name
                    ];
                }
            }
        }

        return response()->json([
            'results' => $results
        ]);
    }
}
