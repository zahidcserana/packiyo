<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderTags extends Model
{
    use SoftDeletes;

    protected $table = 'order_tags';
}
