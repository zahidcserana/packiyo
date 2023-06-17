<?php

namespace App\Components;

use App\Jobs\SyncBulkShipBatchOrders;
use App\Models\BulkShipBatch;
use App\Models\Order;
use App\Models\OrderItem;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BulkShipComponent extends BaseComponent
{
    private int $minSimilarOrders;
    private int $batchOrderLimit;

    public function __construct()
    {
        $this->minSimilarOrders = config('bulk_ship.min_similar_orders');
        $this->batchOrderLimit = config('bulk_ship.batch_order_limit');
    }

    public function syncSingleOrder(Order $order)
    {
        $batchKey = $this->getOrderBatchKey($order);

        $oldBatch = $order->bulkShipBatch()->where('bulk_ship_batches.shipped', 0)->first();

        if (!$oldBatch && !$batchKey) {
            return;
        }

        if ($oldBatch && $oldBatch->batch_key == $batchKey) {
            return;
        } else if ($oldBatch && $oldBatch->batch_key != $batchKey) {
            $oldBatch->orders()->detach($order);
            $oldBatch->touch();

            if ($oldBatch->orders()->count() === 1) {
                $oldBatch->delete();
            }
        }

        if ($existingBatch = BulkShipBatch::where('batch_key', $batchKey)->where('shipped', false)->first()) {
            $existingBatch->orders()->attach($order);
            $existingBatch->touch();
        }
    }

    public function syncBatchOrders(): void
    {
        $batchOrders = $this->getBatchKeysAndOrders(null);

        $updatedOrCreatedBatchOrdersIds = [];

        foreach ($batchOrders as $batchOrdersGroup) {
            $batchOrderIds = explode(',', $batchOrdersGroup->order_ids);
            $batchOrderIds = array_slice($batchOrderIds, 0, $this->batchOrderLimit);

            $batchOrderTotalItems = OrderItem::where('order_id', $batchOrderIds)
                ->sum('quantity_allocated');

            $batch = BulkShipBatch::where('shipped', false)
                ->where('batch_key', $batchOrdersGroup->batch_key)
                ->first();

            $totalOrders = min($batchOrdersGroup->count, $this->batchOrderLimit);

            if ($batch) {
                $batch->update([
                    'total_items' => $batchOrderTotalItems,
                    'total_orders' => $totalOrders,
                ]);
            } else {
                $order = Order::find($batchOrderIds[0]);

                $batch = BulkShipBatch::create([
                    'batch_key' => $batchOrdersGroup->batch_key,
                    'total_items' => $batchOrderTotalItems,
                    'total_orders' => $totalOrders,
                    'customer_id' => $order->customer_id
                ]);
            }

            $updatedOrCreatedBatchOrdersIds[] = $batch->id;

            $batch->orders()->sync($batchOrderIds);
        }

        BulkShipBatch::where('shipped', false)
            ->whereHas('orders', function(Builder $query) {
                return $query->where('shipped', true);
            }, '=', 0)
            ->whereNotIn('id', $updatedOrCreatedBatchOrdersIds)
            ->delete();
    }

    private function getBatchKeysAndOrders(?Order $order = null): Collection
    {
        $whereOrderIdClause = '';
        $havingClause = 'HAVING count > 9';

        if ($order) {
            $whereOrderIdClause = 'AND id = ' . $order->id;
            $havingClause = '';
        }

        DB::statement('SET SESSION group_concat_max_len=1000000');
        $batchOrders = DB::select(/** @lang MySQL */'
            WITH
                filtered_order_items AS (
                    SELECT
                        oi.product_id, oi.quantity_allocated, oi.order_id,
                        toi.id AS tote_order_item_id, NOT ISNULL(toi.id) AS has_tote,
                        pbi.id AS picking_batch_item_id, NOT ISNULL(pbi.id) AS has_picking_batch
                    FROM products p
                    LEFT JOIN
                        order_items oi ON p.id = oi.product_id
                    LEFT JOIN
                        tote_order_items toi ON oi.id = toi.order_item_id
                    LEFT JOIN
                        picking_batch_items pbi ON oi.id = pbi.order_item_id
                    WHERE has_serial_number = 0 AND cancelled_at IS NULL
                ), filtered_orders AS (
                    SELECT
                        o.id, NUMBER, GROUP_CONCAT(foi.product_id, ":", foi.quantity_allocated ORDER BY foi.product_id) AS batch_key
                    FROM orders o
                    LEFT JOIN
                        filtered_order_items foi ON o.id = foi.order_id
                    WHERE quantity_pending_sum > 0
                      AND quantity_allocated_sum > 0
                      AND ready_to_ship = 1
                      AND shipping_method_id IS NOT NULL
                      ' . $whereOrderIdClause . '
                    GROUP BY o.id
                    HAVING SUM(foi.has_tote) = 0 AND SUM(foi.has_picking_batch) = 0
                )
            SELECT
                batch_key, COUNT(*) as count, GROUP_CONCAT(id ORDER BY 1) as order_ids
            FROM filtered_orders
            GROUP BY batch_key
            ' . $havingClause . '
        ');

        return collect($batchOrders);
    }

    private function getOrderBatchKey(Order $order)
    {
        $batchOrders = $this->getBatchKeysAndOrders($order);

        if ($batchOrders->isNotEmpty()) {
            return $batchOrders->first()->batch_key;
        }

        return null;
    }
}
