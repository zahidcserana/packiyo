<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;


/**
 * App\Models\UserRole
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|UserRole newModelQuery()
 * @method static Builder|UserRole newQuery()
 * @method static Builder|UserRole query()
 * @method static Builder|UserRole whereCreatedAt($value)
 * @method static Builder|UserRole whereDeletedAt($value)
 * @method static Builder|UserRole whereId($value)
 * @method static Builder|UserRole whereName($value)
 * @method static Builder|UserRole whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserRole extends Model
{
    public const ROLE_ADMINISTRATOR = 1;
    public const ROLE_MEMBER = 2;

    public const ROLE_DEFAULT = self::ROLE_MEMBER;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Get the users for the role
     *
     * @return HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
