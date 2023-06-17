<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskType\DestroyRequest;
use App\Http\Requests\TaskType\StoreRequest;
use App\Http\Requests\TaskType\UpdateRequest;
use App\Http\Resources\TaskTypeTableResource;
use App\Models\Customer;
use App\Models\TaskType;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TaskTypeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(TaskType::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index()
    {
        return view('task_types.index');
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'task_types.created_at';
        $sortDirection = 'asc';

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $taskCollection = TaskType::join('customers', 'task_types.customer_id', '=', 'customers.id')
            ->join('contact_informations AS customer_contact_information', 'customers.id', '=', 'customer_contact_information.object_id')
            ->where('customer_contact_information.object_type', Customer::class)
            ->select( 'task_types.*')
            ->orderBy($sortColumnName, $sortDirection);

        $customers = app()->user->getSelectedCustomers()->pluck('id')->toArray();

        $taskCollection = $taskCollection->whereIn('task_types.customer_id', $customers);

        $term = $request->get('search')['value'];

        if ($term) {
            // TODO: sanitize term
            $term = $term . '%';

            $taskCollection
                ->orWhereHas('customer.contactInformation', function($query) use ($term) {
                    $query->where('name', 'like', $term);
                })
                ->orWhere('task_types.name', 'like', $term);
        }

        $inventoryLogs = $taskCollection->skip($request->get('start'))->limit($request->get('length'))->get();
        $taskCollection = TaskTypeTableResource::collection($inventoryLogs);

        return response()->json([
            'data' => $taskCollection,
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
        return view('task_types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return Response
     */
    public function store(StoreRequest $request)
    {
        app()->taskType->store($request);

        return redirect()->route('task_type.index')->withStatus(__('Task type successfully created.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param TaskType $taskType
     * @return Factory|Application|\Illuminate\Contracts\View\View
     */
    public function edit(TaskType $taskType)
    {
        return view('task_types.edit', ['taskType' => $taskType]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param TaskType $taskType
     * @return Response
     */
    public function update(UpdateRequest $request, TaskType $taskType)
    {
        app()->taskType->update($request, $taskType);

        return redirect()->back()->withStatus(__('Task type successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRequest $request
     * @param TaskType $taskType
     * @return Response
     */
    public function destroy(DestroyRequest $request, TaskType $taskType)
    {
        app()->taskType->destroy($request, $taskType);

        return redirect()->route('task_type.index')->withStatus(__('Task Type successfully deleted.'));
    }

    public function filterCustomers(Request $request)
    {
        return app()->taskType->filterCustomers($request);
    }
}
