<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\CustomerUserRole
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|CustomerUserRole newModelQuery()
 * @method static Builder|CustomerUserRole newQuery()
 * @method static Builder|CustomerUserRole query()
 * @method static Builder|CustomerUserRole whereCreatedAt($value)
 * @method static Builder|CustomerUserRole whereDeletedAt($value)
 * @method static Builder|CustomerUserRole whereId($value)
 * @method static Builder|CustomerUserRole whereName($value)
 * @method static Builder|CustomerUserRole whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CustomerUserRole extends Model
{
    protected $fillable = ['name'];

    public const ROLE_CUSTOMER_ADMINISTRATOR = 1;
    public const ROLE_CUSTOMER_MEMBER = 2;

    public const ROLE_DEFAULT = self::ROLE_CUSTOMER_MEMBER;
}
