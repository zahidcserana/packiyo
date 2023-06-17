<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Support\Carbon;

/**
 * App\Models\Image
 *
 * @property int $id
 * @property string $object_type
 * @property int $object_id
 * @property string|null $source
 * @property string|null $filename
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Model|\Eloquent $object
 * @method static Builder|Image newModelQuery()
 * @method static Builder|Image newQuery()
 * @method static \Illuminate\Database\Query\Builder|Image onlyTrashed()
 * @method static Builder|Image query()
 * @method static Builder|Image whereCreatedAt($value)
 * @method static Builder|Image whereDeletedAt($value)
 * @method static Builder|Image whereFilename($value)
 * @method static Builder|Image whereId($value)
 * @method static Builder|Image whereObjectId($value)
 * @method static Builder|Image whereObjectType($value)
 * @method static Builder|Image whereSource($value)
 * @method static Builder|Image whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Image withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Image withoutTrashed()
 * @mixin \Eloquent
 */
class Image extends Model implements AuditableInterface
{
    use SoftDeletes, AuditableTrait;

    protected $fillable = [
        'source',
        'filename',
        'object_id',
        'object_type'
    ];

    /**
     * Audit configs
     */
    protected $auditStrict = true;

    protected $auditInclude = [
        'source'
    ];

    public function object()
    {
        return $this->morphTo();
    }

    /**
     * @param array $data
     * @return array
     */
    public function transformAudit(array $data): array
    {
        $data['custom_message'] = '';

        if ($this->auditEvent == 'created') {
            $data['custom_message'] = __('Product image added :source', ['source' => '<a href="' . $this->getAttribute('source') . '" target="_blank">click</a>']);
        } else if ($this->auditEvent == 'deleted') {
            $data['custom_message'] = __('Product image removed :source', ['source' => '<a href="' . $this->getOriginal('source') . '" target="_blank">click</a>']);
        }

        return $data;
    }
}
