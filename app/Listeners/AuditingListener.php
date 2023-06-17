<?php

namespace App\Listeners;

use App\Models\ContactInformation;
use App\Models\Image;
use App\Models\Order;
use App\Models\Product;
use OwenIt\Auditing\Events\Auditing;

class AuditingListener
{
    /**
     * Create the Auditing event listener.
     */
    public function __construct()
    {
        // ...
    }

    /**
     * Handle the Auditing event.
     *
     * @param \OwenIt\Auditing\Events\Auditing $event
     * @return void
     */
    public function handle(Auditing $event)
    {
        if ($event->model->auditEvent == 'sync') {
            $event->model->auditEvent = 'updated';
        }

        if (get_class($event->model) == ContactInformation::class && $event->model->object_type != Order::class) {
            return false;
        } else if (get_class($event->model) == Image::class && $event->model->object_type != Product::class) {
            return false;
        }

        return true;
    }
}
