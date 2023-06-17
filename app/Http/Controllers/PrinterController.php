<?php

namespace App\Http\Controllers;

use App\Http\Dto\Filters\PrintersDataTableDto;
use App\Http\Dto\Filters\PrintJobsDataTableDto;
use App\Http\Resources\PrinterTableResource;
use App\Http\Resources\PrintJobTableResource;
use App\Models\Printer;
use App\Models\PrintJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;


class PrinterController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Printer::class);
    }

    public function index()
    {
        $customers = app()->user->getSelectedCustomers()->pluck('id')->toArray();

        $data = new PrintersDataTableDto(
            Printer::whereIn('customer_id', $customers)->get()->pluck('name', 'id')
        );

        return view('printers.index', [
            'data' => $data,
            'datatableOrder' => app()->editColumn->getDatatableOrder('printers'),
        ]);
    }

    public function dataTable(Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'printers.name';
        $sortDirection = 'asc';
        $term = $request->get('search')['value'];
        $filterInputs =  $request->get('filter_form');
        $printersQuery = Printer::where('user_id', auth()->user()->id);

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        if ($term) {
            $term = $term . '%';
            $printersQuery->where('name', 'like', $term);
            $printersQuery->orWhere('hostname', 'like', $term);
        }

        if (Arr::get($filterInputs, 'created_date')) {
            $printersQuery->where('created_at', '>=', $filterInputs['created_date']);
        }

        $printersQuery->orderBy($sortColumnName, $sortDirection);

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $printersQuery->skip($request->get('start'))->limit($request->get('length'));
        }

        $printers = $printersQuery->get();
        $printerCollection = PrinterTableResource::collection($printers);
        $visibleFields = app()->editColumn->getVisibleFields('printers');

        return response()->json([
            'data' => $printerCollection,
            'visibleFields' => $visibleFields,
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    public function disable(Printer $printer)
    {
        $this->authorize('disable', $printer);

        app('printer')->disable($printer);

        return redirect()->route('printer.index')->withStatus(__('Printer successfully disabled.'));
    }

    public function enable(Printer $printer)
    {
        $this->authorize('enable', $printer);

        app('printer')->enable($printer);

        return redirect()->route('printer.index')->withStatus(__('Printer successfully enabled.'));
    }

    public function jobs(Printer $printer)
    {
        $this->authorize('jobs', $printer);

        $data = new PrintJobsDataTableDto(
            PrintJob::where('printer_id', $printer->id)->get()
        );

        return view('printers.jobs', [
            'data' => $data,
            'printer' => $printer,
            'datatableOrder' => app()->editColumn->getDatatableOrder('print_jobs'),
        ]);
    }

    public function jobsDataTable(Printer $printer, Request $request)
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'print_jobs.created_at';
        $sortDirection = 'asc';
        $term = $request->get('search')['value'];
        $filterInputs =  $request->get('filter_form');

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $printJobQuery = PrintJob::where('printer_id', $printer->id);
        if ($term) {
            $term = $term . '%';
            $printJobQuery->where(function($query) use ($term){
                $query->where('status', 'like', $term);
                $query->orWhere('url','like', $term);
            });
        }

        if (Arr::get($filterInputs, 'created_date')) {
            $printJobQuery->where('created_at', '>=', $filterInputs['created_date']);
        }

        $printJobQuery->orderBy($sortColumnName, $sortDirection);

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $printJobQuery->skip($request->get('start'))->limit($request->get('length'));
        }

        $printJobs = $printJobQuery->get();
        $printJobsCollection = PrintJobTableResource::collection($printJobs);

        $visibleFields = app()->editColumn->getVisibleFields('print_jobs');

        return response()->json([
            'data' => $printJobsCollection,
            'visibleFields' => $visibleFields,
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    public function jobRepeat(PrintJob $printJob)
    {
        $this->authorize('jobRepeat', $printJob);

        $newJob = app('printer')->repeatJob($printJob);

        return redirect()->route('printer.jobs', ['printer' => $newJob->printer])->withStatus(__('Job successfully created.'));
    }
}
