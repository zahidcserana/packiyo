<?php

namespace App\Http\Controllers;

use App\Http\Resources\PickingBatchItemTableResource;
use App\Models\PickingBatch;
use App\Models\PickingBatchItem;
use App\Models\Task;
use App\Models\OrderLock;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class PickingBatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Factory|\Illuminate\View\View
     */
    public function getItems(PickingBatch $pickingBatch)
    {
        return view('picking_batch.items', [
            'page' => 'picking_batch_items',
            'pickingBatch' => $pickingBatch,
            'datatableOrder' => app()->editColumn->getDatatableOrder('picking-batch-items'),
        ]);
    }

    public function dataTable(PickingBatch  $pickingBatch, Request $request): JsonResponse
    {
        $tableColumns = $request->get('columns');
        $columnOrder = $request->get('order');
        $sortColumnName = 'picking_batch_items.created_at';
        $sortDirection = 'asc';
        $term = $request->get('search')['value'];

        if (!empty($columnOrder)) {
            $sortColumnName = $tableColumns[$columnOrder[0]['column']]['name'];
            $sortDirection = $columnOrder[0]['dir'];
        }

        $pickingBatchItemQuery = PickingBatchItem::withTrashed()->with([
            'pickingBatch',
            'location',
            'toteOrderItems.location',
            'toteOrderItems.tote',
            'orderItem.order'
        ])
            ->leftJoin('order_items', 'order_items.id', '=', 'picking_batch_items.order_item_id')
            ->leftJoin('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('tote_order_items', 'tote_order_items.picking_batch_item_id', '=', 'picking_batch_items.id')
            ->leftJoin('totes', 'totes.id', '=', 'tote_order_items.tote_id')
            ->where('picking_batch_items.picking_batch_id', $pickingBatch->id)
            ->when(!empty($term), static function (Builder $query) use ($term) {
                $term = $term . '%';

                $query->whereHas('orderItem', function ($q) use ($term) {
                    $q->where('sku', 'like', $term)
                    ->orWhereHas('order', function ($q2) use ($term) {
                        $q2->where('number', 'like', $term);
                    });;
                });
            })
            ->select('picking_batch_items.*')
            ->orderBy($sortColumnName, $sortDirection);

        if ($request->get('length') && ((int) $request->get('length')) !== -1) {
            $pickingBatchItemQuery = $pickingBatchItemQuery->skip($request->get('start'))->limit($request->get('length'));
        }

        $pickingBatchItems = $pickingBatchItemQuery->get();

        $pickingBatchItemCollection = PickingBatchItemTableResource::collection($pickingBatchItems);

        return response()->json([
            'data' => $pickingBatchItemCollection,
            'visibleFields' => app()->editColumn->getVisibleFields('picking-batch-items'),
            'recordsTotal' => PHP_INT_MAX,
            'recordsFiltered' => PHP_INT_MAX
        ]);
    }

    public function clearBatch(PickingBatch $pickingBatch): JsonResponse
    {
        app('pickingBatch')->closePickingTask($pickingBatch);

        return response()->json([
            'success' => true,
            'message' => __('Picking Batch cleared successfully!')
        ]);
    }
}
