<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RecalculateOrderReadyToShip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recalculate-ready-to-ship';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate Orders ready to ship value';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        app('order')->recalculateReadyToShipOrders();
    }
}
