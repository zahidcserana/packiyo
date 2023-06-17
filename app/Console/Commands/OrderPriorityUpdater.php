<?php

namespace App\Console\Commands;
use App\Models\Order;
use Illuminate\Console\Command;

class OrderPriorityUpdater extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order-priority-updater';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updating orders priority based on order date';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function handle()
    {
        $orders = Order::whereNull('fulfilled_at')
            ->whereNull('cancelled_at');

        foreach ($orders->cursor() as $order) {
            app('order')->updatePriorityScore($order);
        }
    }

}
