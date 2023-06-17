<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStatus\DestroyRequest;
use App\Http\Requests\OrderStatus\StoreRequest;
use App\Http\Requests\OrderStatus\UpdateRequest;
use App\Http\Resources\OrderStatusTableResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderStatus;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class OrderStatusController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(OrderStatus::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index()
    {
        return view('order_status.index', [
            'page' => 'order_statuses',
            'datatableOrder' => app()->editColumn->getDatatableOrder('order-status'),
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'order_statuses.name';
        $sortDirection = 'asc';
        $term = $request->get('search')['value'];
        $filterInputs =  $request->get('filter_form');

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $orderStatusCollection = OrderStatus::join('customers', 'order_statuses.customer_id', '=', 'customers.id')
            ->join('contact_informations AS customer_contact_information', 'customers.id', '=', 'customer_contact_information.object_id')
            ->where('customer_contact_information.object_type', Customer::class)
            ->groupBy('order_statuses.id')
            ->select('order_statuses.*')
            ->orderBy($sortColumnName, $sortDirection);

        $customers = app('user')->getSelectedCustomers()->pluck('id')->toArray();

        $orderStatusCollection = $orderStatusCollection->whereIn('order_statuses.customer_id', $customers);

        if ($term) {
            $term = $term . '%';

            $orderStatusCollection->where(function ($query) use ($term) {
                $query
                    ->where('order_statuses.name', 'like', $term)
                    ->orWhereHas('customer.contactInformation', function ($q) use ($term) {
                        $q->where('name', 'like', $term);
                    });
            });
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $orderStatusCollection = $orderStatusCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $orderStatuses = $orderStatusCollection->get();
        $orderStatusCollection = OrderStatusTableResource::collection($orderStatuses);

        return response()->json([
            'data' => $orderStatusCollection,
            'visibleFields' => app('editColumn')->getVisibleFields('order-status'),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('order_status.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return Response
     */
    public function store(StoreRequest $request)
    {
        app()->orderStatus->store($request);

        return redirect()->route('order_status.index')->withStatus(__('Order status successfully created.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param OrderStatus $orderStatus
     * @return Factory|View
     */
    public function edit(OrderStatus $orderStatus)
    {
        return view('order_status.edit', ['orderStatus' => $orderStatus]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param OrderStatus $orderStatus
     * @return Response
     */
    public function update(UpdateRequest $request, OrderStatus $orderStatus)
    {
        app()->orderStatus->update($request, $orderStatus);

        return redirect()->back()->withStatus(__('Order status successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRequest $request
     * @param OrderStatus $orderStatus
     * @return Response
     */
    public function destroy(DestroyRequest $request ,OrderStatus $orderStatus)
    {
        app()->orderStatus->destroy($request, $orderStatus);

        return redirect()->back()->withStatus(__('Order status successfully deleted.'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function filterCustomers(Request $request): JsonResponse
    {
        $term = $request->get('term');
        $results = [];


        if ($term) {
            $contactInformation = Customer::whereHas('contactInformation', static function($query) use ($term) {
                $query->where('name', 'like', $term . '%' )
                    ->orWhere('company_name', 'like',$term . '%')
                    ->orWhere('email', 'like',  $term . '%' )
                    ->orWhere('zip', 'like', $term . '%' )
                    ->orWhere('city', 'like', $term . '%' )
                    ->orWhere('phone', 'like', $term . '%' );
            })->get();


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
