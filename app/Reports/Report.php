<?php

namespace App\Reports;

use App\Models\Shipment;
use App\Models\ShipmentItem;
use Dflydev\DotAccessData\Data;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class Report implements ReportInterface
{
    protected $reportId;
    protected $dataTableResourceClass;
    protected $exportResourceClass;
    protected $widget;

    protected function reportTitle()
    {
        return '';
    }

    public function view(Request $request): Factory|View|Application
    {
        $filterDto = $this->getFilterDto($request);

        return view('reports.view', [
            'reportId' => $this->reportId,
            'reportTitle' => $this->reportTitle(),
            'datatableOrder' => app('editColumn')->getDatatableOrder($this->reportId),
            'data' => $filterDto,
            'widgetsUrl' => $this->widget ? route('report.widgets', ['reportId' => $this->reportId]) : ''
        ]);
    }

    public function widgets(Request $request): Factory|View|Application
    {
        $data = $this->getWidgetsQuery($request);

        return view('reports.widgets.shipments', [
            'shipmentCount' => $data['shipmentCount'],
            'totalItemsShipped' => $data['totalItemsShipped'] ?? 0,
            'distinctItemsShipped' => $data['distinctItemsShipped'],
        ]);
    }

    protected function getWidgetsQuery(Request $request): array
    {
        return [];
    }

    protected function getQuery(Request $request): ?Builder
    {
        return null;
    }

    protected function getFilterDto(Request $request): ?Arrayable
    {
        return null;
    }

    public function dataTable(Request $request): JsonResponse
    {
        $query = $this->getQuery($request);

        $start = $request->get('start');
        $length = $request->get('length');

        if ($length == -1) {
            $length = 10;
        }

        if ($length) {
            $query = $query->skip($start)->limit($length);
        }

        return response()->json([
            'data' => $this->dataTableResourceClass::collection($query->get()),
            'visibleFields' => app('editColumn')->getVisibleFields($this->reportId),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $filename = Str::kebab(auth()->user()->contactInformation->name) . '-' . $this->reportId . '-' . now()->toDateTimeString() . '.csv';

        return app('csv')->export(
            $request,
            $this->getQuery($request)->get(),
            $this->exportResourceClass::columns(),
            $filename,
            $this->exportResourceClass
        );
    }
}
