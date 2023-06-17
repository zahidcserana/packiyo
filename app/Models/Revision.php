<?php

namespace App\Models;

use Venturecraft\Revisionable\Revision as RevisionableRevision;

class Revision extends RevisionableRevision
{
    public function getNote()
    {
        $note = '';
        $fieldName = str_replace('_', ' ', $this->fieldName());

        if ($this->revisionable_type === Product::class && $this->is_image) {
            // Product image
            if ($this->action === 'Added') {
                $note = __('Added Product Image') . ' ' . $this->newValue() . ' ' . __('to') . ' ' . $fieldName;
            } else {
                $note = __('Removed Product Image') . ' ' . $this->newValue() . ' ' . __('from') . ' ' . $fieldName;
            }
        }
        else if (empty($this->oldValue()) && empty($this->oldValue())) {
            // Profile image
            if($this->revisionable_type === User::class && $fieldName === 'picture') {
                $note = __('Added profile image');
            } else {
                $note = __('Added') . ' ' . $this->newValue() . ' ' . __('to') . ' ' . $fieldName;
            }
        } else if (empty($this->newValue()) && empty($this->newValue())) {
            $note = __('Removed') . ' ' . $this->oldValue() . ' ' . __('from') . ' ' . $fieldName;
        } else {
            // Profile image
            if ($fieldName === 'picture') {
                $note = __('Changed profile image');
            } else {
                $note = __('Changed') . ' ' .$fieldName . ' ' . __('from') . ' ' . ($this->oldValue() ?? __('null')) . ' ' . __('to') . ' ' . $this->newValue();
            }
        }

        return $note;
    }

    public function getType()
    {
        $type = '';

        switch ($this->revisionable_type) {

            case OrderItem::class:
                $type = 'Order Item';
                $type .= ' (#'.$this->revisionable->product->sku.' '.$this->revisionable->name.')';
                break;

            case Product::class:
                $type = 'Product';
                $type .= ' (#'.$this->revisionable->sku.' '.$this->revisionable->name.')';
                break;

            case PurchaseOrderItem::class:
                $type = 'Purchase Order Item';
                $type .= ' (#'.$this->revisionable->product->sku.' '.$this->revisionable->product->name.')';
                break;

            case Order::class:
                $type = 'Order';
                $type .= ' (#'.$this->revisionable->number.')';
                break;

            case User::class:
                $type = 'User';
                break;

            case ContactInformation::class:
                $type = 'Contact Information';
                break;

            default:
                $type = $this->revisionable_type;
                break;

        }

        return $type;
    }
}
