<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\TaskType;

class CustomerObserver
{
    /**
     * Handle the Customer "saved" event.
     *
     * @param Customer $customer
     * @return void
     */
    public function saved(Customer $customer): void
    {
        TaskType::firstOrCreate([
            'name' => 'Picking',
            'type' => TaskType::TYPE_PICKING,
            'customer_id' => $customer->id
        ]);

        TaskType::firstOrCreate([
            'name' => 'Packing',
            'type' => TaskType::TYPE_PACKING,
            'customer_id' => $customer->id
        ]);

        TaskType::firstOrCreate([
            'name' => 'Counting Products',
            'type' => TaskType::TYPE_COUNTING_PRODUCTS,
            'customer_id' => $customer->id
        ]);

        TaskType::firstOrCreate([
            'name' => 'Counting Locations',
            'type' => TaskType::TYPE_COUNTING_LOCATIONS,
            'customer_id' => $customer->id
        ]);
    }
}
