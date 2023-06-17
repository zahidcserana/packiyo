<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EditColumn extends Model
{
    public const VISIBLE_FIELDS = [
        '1', '2', '3', '4'
    ];

    protected $fillable = [
        'object_type',
        'visible_fields',
        'order_column,',
        'order_direction',
    ];
}
