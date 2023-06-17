<?php

namespace App\Http\Controllers;

use App\Reports\PackerReport;
use App\Reports\PickerReport;
use App\Reports\ReplenishmentReport;
use App\Reports\Report;
use App\Reports\SerialNumberReport;
use App\Reports\ShipmentReport;
use App\Reports\ShippedItemReport;
use App\Reports\StaleInventoryReport;
use App\Reports\PickingBatchReport;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{
    private const REPORTS = [
        'shipment' => ShipmentReport::class,
        'shipped_item' => ShippedItemReport::class,
        'picker' => PickerReport::class,
        'packer' => PackerReport::class,
        'replenishment' => ReplenishmentReport::class,
        'stale_inventory' => StaleInventoryReport::class,
        'serial_number' => SerialNumberReport::class,
        'picking_batch' => PickingBatchReport::class
    ];

    public function view(Request $request, $reportId)
    {
        if ($report = $this->getReportInstance($reportId)) {
            return $report->view($request);
        }

        abort(404);
    }

    public function widgets(Request $request, $reportId)
    {
        if ($report = $this->getReportInstance($reportId)) {
            return $report->widgets($request);
        }

        abort(404);
    }

    public function dataTable(Request $request, $reportId)
    {
        if ($report = $this->getReportInstance($reportId)) {
            return $report->dataTable($request);
        }

        abort(404);
    }

    public function export(Request $request, $reportId)
    {
        if ($report = $this->getReportInstance($reportId)) {
            return $report->export($request);
        }

        abort(404);
    }

    private function getReportInstance($reportId): ?Report
    {
        /** @var Report $reportClass */
        $reportClass = Arr::get(self::REPORTS, $reportId);

        if ($reportClass) {
            return new $reportClass;
        }

        return null;
    }
}
