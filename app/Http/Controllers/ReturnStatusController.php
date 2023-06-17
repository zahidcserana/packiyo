<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReturnStatus\DestroyRequest;
use App\Http\Requests\ReturnStatus\StoreRequest;
use App\Http\Requests\ReturnStatus\UpdateRequest;
use App\Http\Resources\ReturnStatusTableResource;
use App\Models\Customer;
use App\Models\ReturnStatus;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ReturnStatusController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ReturnStatus::class);
    }

    public function index()
    {
        return view('return_status.index', [
            'page' => 'return_statuses',
            'datatableOrder' => app()->editColumn->getDatatableOrder('return-status'),
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'return_statuses.id';
        $sortDirection = 'desc';

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $term = $request->get('search')['value'];

        $returnStatusCollection = ReturnStatus::join('customers', 'return_statuses.customer_id', '=', 'customers.id')
            ->join('contact_informations AS customer_contact_information', 'customers.id', '=', 'customer_contact_information.object_id')
            ->where('customer_contact_information.object_type', Customer::class)
            ->groupBy('return_statuses.id')
            ->select('return_statuses.*')
            ->orderBy($sortColumnName, $sortDirection);

        $customer = app()->user->getSelectedCustomers();

        if ($customer) {
            $customers = $customer->pluck('id')->toArray();

            $returnStatusCollection = $returnStatusCollection->whereIn('return_statuses.customer_id', $customers);
        }

        if ($term) {
            $term = $term . '%';
            $returnStatusCollection->where(function ($q) use ($term) {
                $q->where('return_statuses.name', 'like', $term)
                    ->orWhereHas('customer.contactInformation', function ($query) use ($term) {
                        $query->where('name', 'like', $term);
                    });
            });
        }

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $returnStatusCollection = $returnStatusCollection->skip($request->get('start'))->limit($request->get('length'));
        }

        $returnStatuses = $returnStatusCollection->get();

        return response()->json([
            'data' => ReturnStatusTableResource::collection($returnStatuses),
            'visibleFields' => app()->editColumn->getVisibleFields('return-status'),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    public function create()
    {
        return view('return_status.createEdit', ['returnStatus' => null]);
    }

    public function store(StoreRequest $request)
    {
        app()->returnStatus->store($request);

        return response()->json([
            'success' => true,
            'message' => __('Return status successfully created.')
        ]);
    }

    public function edit(ReturnStatus $returnStatus)
    {
        return view('return_status.createEdit', ['returnStatus' => $returnStatus]);
    }

    public function update(UpdateRequest $request, ReturnStatus $returnStatus)
    {
        app()->returnStatus->update($request, $returnStatus);

        return response()->json([
            'success' => true,
            'message' => __('Return status successfully updated.')
        ]);
    }

    public function destroy(DestroyRequest $request ,ReturnStatus $returnStatus)
    {
        app()->returnStatus->destroy($request, $returnStatus);

        return back()->withStatus('Return status successfully deleted.');
    }

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
