<?php

namespace App\Http\Requests\PickingBatch;

use App\Http\Requests\FormRequest;
use App\Models\PickingBatchItem;
use App\Models\Task;
use App\Models\Tote;
use Illuminate\Validation\Rule;

class PickRequest extends FormRequest
{
    public static function validationRules()
    {
        $type = static::getInputField('type');
        [$order] = static::getInputField('orders');
        $toteId = static::getInputField('tote_id');
        $pickingBatchId = static::getInputField('picking_batch_id');
        $tote = Tote::find($toteId);

        switch ($type) {
            case 'sib':
                if ($toteOrderItem = $tote->placedToteOrderItems->first()) {
                    $pickingBatchItem = PickingBatchItem::find($toteOrderItem->picking_batch_item_id);

                    if ($pickingBatchItem->picking_batch_id !== $pickingBatchId) {
                        $toteId = '';
                    }
                }
                break;
            default:
                if ($toteOrderItem = $tote->placedToteOrderItems->first()) {
                    if ($toteOrderItem->orderItem->order_id !== $order['id']) {
                        $toteId = '';
                    }
                }
        }

        $rules = [
            'picking_batch_id' => [
                'required'
            ],
            'tote_id' => [
                'required',
                Rule::in([$toteId]),
            ],
            'product_id' => [
                'required',
            ],
            'location_id' => [
                'required',
            ],
            'orders' => [
                'required'
            ],
            'type' => [
                'required'
            ]
        ];

        if (!Task::where('taskable_id', $pickingBatchId)->where('completed_at', null)->count()) {
            $rules = array_merge($rules, ['picking_batch_completed' => [
                'required',
                'picking_batch_completed'
            ]]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'picking_batch_completed' => 'Picking batch is already completed.'
        ];
    }
}
