<?php

namespace Shelter\Guard\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Shelter\Kernel\Database\AbstractUUIDModel;
use Shelter\Guard\Models\UserRole;

/**
 * Class Permission
 * @package Shelter\Guard\Models
 *
 * @property string $key
 * @property-read UserRole $role
 */
class Permission extends AbstractUUIDModel
{
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(UserRole::class);
    }
}
