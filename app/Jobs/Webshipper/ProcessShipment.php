<?php

namespace App\Jobs\Webshipper;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Shipment;

class ProcessShipment implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $shipment;
    public $shippingRateId;
    public $returnErrors;
    public $printerId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Shipment $shipment, $shippingRateId, $returnErrors = false, $printerId = 0)
    {
        $this->shipment = $shipment;
        $this->shippingRateId = $shippingRateId;
        $this->returnErrors = $returnErrors;
        $this->printerId = $printerId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app()->webshipperShipping->processShipment($this->shipment, $this->shippingRateId, $this->returnErrors, $this->printerId);
    }

    public function uniqueId()
    {
        return $this->shipment->id;
    }
}

