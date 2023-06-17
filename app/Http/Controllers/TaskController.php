<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\DestroyRequest;
use App\Http\Requests\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Resources\TaskTableResource;
use App\Models\User;
use App\Models\Customer;
use App\Models\Task;
use App\Models\TaskType;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('tasks.index');
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'tasks.created_at';
        $sortDirection = 'asc';

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $taskCollection = Task::join('users', 'tasks.user_id', '=', 'users.id')
            ->join('contact_informations AS user_contact_information', 'users.id', '=', 'user_contact_information.object_id')
            ->join('customers', 'tasks.customer_id', '=', 'customers.id')
            ->join('contact_informations AS customer_contact_information', 'customers.id', '=', 'customer_contact_information.object_id')
            ->join('task_types', 'tasks.task_type_id', '=', 'task_types.id')
            ->where('user_contact_information.object_type', User::class)
            ->where('customer_contact_information.object_type', Customer::class)
            ->select('tasks.*')
            ->orderBy($sortColumnName, $sortDirection);

        $customers = app()->user->getSelectedCustomers()->pluck('id')->toArray();

        $taskCollection = $taskCollection->whereIn('tasks.customer_id', $customers);

        $term = $request->get('search')['value'];

        if ($term) {
            // TODO: sanitize term
            $term = $term . '%';

            $taskCollection
                ->whereHas('user.contactInformation', function($query) use ($term) {
                    $query->where('name', 'like', $term);
                })
                ->orWhereHas('customer.contactInformation', function($query) use ($term) {
                    $query->where('name', 'like', $term);
                })
                ->orWhereHas('taskType', function($query) use ($term) {
                    $query->where('name', 'like', $term);
                })
                ->orWhere('tasks.notes', 'like', $term);
        }

        $inventoryLogs = $taskCollection->skip($request->get('start'))->limit($request->get('length'))->get();
        $taskCollection = TaskTableResource::collection($inventoryLogs);

        return response()->json([
            'data' => $taskCollection,
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $taskTypes = TaskType::all();

        return view('tasks.create', ['taskTypes' => $taskTypes]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return Response
     */
    public function store(StoreRequest $request)
    {
        app()->task->store($request);

        return redirect()->route('task.index')->withStatus(__('Task successfully created.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Task $task
     * @return Application|Factory|View
     */
    public function edit(Task $task)
    {
        $taskTypes = TaskType::all();

        return view('tasks.edit', ['task' => $task, 'taskTypes' => $taskTypes]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Task $task
     * @return Response
     */
    public function update(UpdateRequest $request, Task $task)
    {
        app()->task->update($request, $task);

        return redirect()->back()->withStatus(__('Task successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DestroyRequest $request
     * @param Task $task
     * @return Response
     */
    public function destroy(DestroyRequest $request, Task $task)
    {
        app()->task->destroy($request, $task);

        return redirect()->route('task.index')->withStatus(__('Task successfully deleted.'));
    }

    public function filterUsers(Request $request)
    {
        return app()->task->filterUsers($request);
    }

    public function filterCustomers(Request $request)
    {
        return app()->task->filterCustomers($request);
    }
}
