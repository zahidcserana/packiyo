<?php

namespace App\Components;

use App\Models\EditColumn;

class EditColumnComponent extends BaseComponent
{
    public function update($request)
    {
        $object = $request->get('object');
        $visibleIds = $request->get('visibleIds');
        $orderColumn = $request->get('orderColumn');
        $orderDirection = $request->get('orderDirection');

        if (!empty($object)) {
            $item = EditColumn::query()
                ->where([
                    'object_type' => $object,
                    'user_id' => auth()->user()->id
                ])
                ->first();

            if (! empty($item)) {
                $model = $item;
            } else {
                $model = new EditColumn();
                $model->user_id = auth()->user()->id;
                $model->object_type = $object;
            }

            $model->order_column = $orderColumn;
            $model->order_direction = $orderDirection;
            $model->visible_fields = json_encode($visibleIds);
            $model->save();
        }
    }

    public function getVisibleFields($object): array
    {
        $item = EditColumn::query()
            ->where([
                'object_type' => $object,
                'user_id' => auth()->user()->id
            ])
            ->first();

        if ((! empty($item)) && $item->visible_fields !== 'null') {
            $response = json_decode($item->visible_fields);
        } else {
            $response =  EditColumn::VISIBLE_FIELDS;
        }

        return [
            'column_ids' => array_map('intval', $response),
            'order_column' => $item->order_column ?? '',
            'order_direction' => $item->order_direction ?? '',
        ];
    }

    public function getDatatableOrder($object): ?array
    {
        $fields = $this->getVisibleFields($object);

        if (in_array('', [$fields['order_column'], $fields['order_direction']])) {
            return null;
        }

        return [$fields['order_column'], $fields['order_direction']];
    }
}
