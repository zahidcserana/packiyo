<?php

namespace App\Console\Commands;

use App\Jobs\SyncBulkShipBatchOrders;
use Illuminate\Console\Command;

class SyncBatchOrders extends Command
{
    protected $signature = 'sync:batch-orders';

    protected $description = '
        Recheck db for orders that match. Orders with the same order items
        (product_id, quantity_allocated) will be matched as batch orders.
    ';

    public function handle(): int
    {
        SyncBulkShipBatchOrders::dispatchSync();

        return 0;
    }
}
