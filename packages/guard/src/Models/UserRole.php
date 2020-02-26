<?php

namespace Shelter\Guard\Models;

use Shelter\Kernel\Database\AbstractUUIDModel;

use Illuminate\Database\Eloquent\{
    Collection,
    Relations\BelongsToMany,
    Relations\HasMany,
    Relations\MorphMany
};

/**
 * Class Role
 * @package Shelter\Guard\Models
 *
 * @property string $id
 * @property string $title
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection $permissions
 * @property Collection $users
 */
class UserRole extends AbstractUUIDModel
{
    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @param array $keys
     * @return void
     */
    public function syncPermissions(array $keys = []): void
    {
        $this->permissions()->delete();

        $permissions = [];

        foreach ($keys as $permission) {
            $permissions[] = [
                'key' => $permission,
            ];
        }

        if (! empty($permissions)) {
            $this->permissions()->createMany($permissions);
        }
    }

    /**
     * @return MorphMany
     */
    public function permissions(): MorphMany
    {
        return $this->morphMany(
            Permission::class,
            'permissionable'
        );
    }

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class
        );
    }
}
