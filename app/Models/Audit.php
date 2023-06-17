<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model implements \OwenIt\Auditing\Contracts\Audit
{
    use \OwenIt\Auditing\Audit;

    protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * {@inheritdoc}
     */
    protected $guarded = [];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'old_values'   => 'json',
        'new_values'   => 'json',
        'custom_message' => 'string',
        'auditable_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * {@inheritdoc}
     */
    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * {@inheritdoc}
     */
    public function parentable()
    {
        return $this->morphTo();
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        return $this->morphTo();
    }

    public function getDisplayDateAttribute()
    {
        return user_date_time($this->created_at->toDateString(), true);
    }
}
