<?php

namespace App\Components;

use App\Http\Requests\ShippingMethod\UpdateRequest;
use App\Http\Resources\ShippingMethodResource;
use App\Models\ShippingMethod;
use App\Models\Webhook;
use Illuminate\Support\Arr;

class ShippingMethodComponent extends BaseComponent
{
    public function update(UpdateRequest $request, ShippingMethod $shippingMethod, $fireWebhook = true): ShippingMethod
    {
        $input = $request->validated();

        $this->updateTags(Arr::get($input, 'tags'), $shippingMethod);

        if ($fireWebhook) {
            $this->webhook(
                new ShippingMethodResource($shippingMethod),
                ShippingMethod::class,
                Webhook::OPERATION_TYPE_UPDATE,
                $shippingMethod->customer_id
            );
        }

        return $shippingMethod;
    }
}
