<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Resources\BulkShippingTableResource;
use App\Models\BulkShipBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class BulkShippingController extends Controller
{
    public function index(Request $request, $shipped = false)
    {
        if (!$request->ajax()) {
            return view('bulk_shipping.index', [
                'page' => 'bulk_shipping',
                'datatableOrder' => app()->editColumn->getDatatableOrder('bulk-shipping'),
            ]);
        }

        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'created_at';
        $sortDirection = 'desc';
        $filterInputs =  $request->get('filter_form');

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $customers = app()->user->getSelectedCustomers()->pluck('id')->toArray();

        $bulkShipBatchQuery = BulkShipBatch::query()
            ->whereIn('customer_id', $customers)
            ->where('shipped_at', $shipped ? '!=' : '=', null)
            ->when($filterInputs['printed'] ?? null, function($query, $printed) {
                if ($printed === 'no') {
                    return $query->whereNull('printed_user_id');
                }

                return $query->whereNotNull('printed_user_id');
            })
            ->when($filterInputs['packed'] ?? null, function($query, $packed) {
                if ($packed === 'no') {
                    return $query->whereNull('packed_user_id');
                }

                return $query->whereNotNull('packed_user_id');
            })
            ->with('orders.orderItems', 'orders.customer.contactInformation')
            ->where(function($query) use ($filterInputs) {
                // Find by filter result
                // Start/End date
                if (Arr::get($filterInputs, 'start_date') || Arr::get($filterInputs, 'end_date')) {
                    $startDate = Carbon::parse($filterInputs['start_date'] ?? '1970-01-01')->startOfDay();
                    $endDate = Carbon::parse($filterInputs['end_date'] ?? Carbon::now())->endOfDay();

                    $query->whereBetween('bulk_ship_batches.updated_at', [$startDate, $endDate]);
                }
            })
            ->orderBy($sortColumnName, $sortDirection);

        $term = $request->get('search')['value'];

        if ($term) {
            $term = $term . '%';
            $bulkShipBatchQuery = $bulkShipBatchQuery->whereHas('orders', function($query) use ($term) {
                    return $query->where('number', 'LIKE', $term);
                })->orWhereHas('orders.orderItems', function($query) use ($term) {
                    return $query->where('sku', 'LIKE', $term);
                })->orWhere('id', $term);
        }

        if ($request->get('length') && ((int)$request->get('length')) !== -1) {
            $bulkShipBatchQuery = $bulkShipBatchQuery->skip($request->get('start'))->limit($request->get('length'));
        }

        $bulkShipBatchQuery = $bulkShipBatchQuery->get();
        $bulkShipBatchCollection = BulkShippingTableResource::collection($bulkShipBatchQuery);

        return response()->json([
            'data' => $bulkShipBatchCollection,
            'visibleFields' => app()->editColumn->getVisibleFields('bulk-shipping'),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX,
        ]);
    }

    public function batches(Request $request)
    {
        return $this->index($request, true);
    }

    public function markAsPrinted(BulkShipBatch $bulkShipBatch)
    {
        $bulkShipBatch->update([
            'printed_user_id' => Auth::id(),
            'printed_at' => now(),
        ]);

        return response()->json([
            'message' => __('Batch marked as printed!'),
        ]);
    }

    public function markAsPacked(BulkShipBatch $bulkShipBatch)
    {
        $bulkShipBatch->update([
            'packed_user_id' => Auth::id(),
            'packed_at' => now(),
        ]);

        return response()->json([
            'message' => __('Batch marked as packed!'),
        ]);
    }
}
