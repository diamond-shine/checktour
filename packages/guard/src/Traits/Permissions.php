<?php

namespace Shelter\Guard\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as SupportCollection;
use Shelter\Guard\Models\Permission;
use Shelter\Guard\Models\UserRole;

trait Permissions
{
    use Authorizable {
        can as originCan;
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
     * @param string|UserRole $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        if ($role instanceof UserRole) {
            return $this->roles->contains(
                'id',
                $role->getKey()
            );
        }

        return $this->roles->contains('name', $role);
    }

    /**
     * @param mixed ...$roles
     * @return bool
     */
    public function hasAnyRole(...$roles): bool
    {
        foreach (collect($roles)->flatten() as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return SupportCollection
     */
    public function allPermissions(): SupportCollection
    {
        $permissions = $this->permissions->pluck('key')->toArray();

        foreach ($this->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission->key;
            }
        }

        return collect(
            \array_unique($permissions)
        );
    }

    /**
     * @param string $permissions
     * @return bool
     */
    public function containsPermission(string $permissions): bool
    {
        return $this->allPermissions()->contains($permissions);
    }

    /**
     * @param mixed ...$permissions
     * @return bool
     */
    public function containsAnyPermission(...$permissions): bool
    {
        $permissions = Arr::flatten($permissions);

        return $this->allPermissions()->intersect($permissions)->isNotEmpty();
    }

    /**
     * @param mixed ...$permissions
     * @return bool
     */
    public function containsAllPermissions(...$permissions): bool
    {
        $permissions = Arr::flatten($permissions);

        return \count($permissions) === $this->allPermissions()->intersect($permissions)->count();
    }

    /**
     * @param mixed ...$roles
     * @return bool
     */
    public function hasAllRoles(...$roles): bool
    {
        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) {
                if ($role instanceof UserRole) {
                    return $role->name;
                }

                return $role;
            });

        return $roles->intersect($this->roles->pluck('name')) === $roles;
    }

    /**
     * @param string $ability
     * @param array|mixed $arguments
     * @return bool
     */
    public function can($ability, $arguments = []): bool
    {
        return app('shelter.permissions')->can($this, $ability, $arguments)
            || $this->originCan($ability, $arguments);
    }

    /**
     * @param array $abilities
     * @return bool
     */
    public function canAll(array $abilities): bool
    {
        foreach ($abilities as $index => $ability) {
            $arguments = [];

            if (\is_array($ability)) {
                $arguments = $ability;
                $ability = $index;
            }

            if (! app('shelter.permissions')->can($this, $ability, $arguments)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $abilities
     * @return bool
     */
    public function canAny(array $abilities): bool
    {
        foreach ($abilities as $index => $ability) {
            $arguments = [];

            if (\is_array($ability)) {
                $arguments = $ability;
                $ability = $index;
            }

            if (app('shelter.permissions')->can($this, $ability, $arguments)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $ability
     * @param array $arguments
     * @return bool
     */
    public function cannot($ability, $arguments = []): bool
    {
        return $this->cant($ability, $arguments);
    }

    /**
     * @param string $ability
     * @param array $arguments
     * @return bool
     */
    public function cant($ability, $arguments = []): bool
    {
        return ! $this->can($ability, $arguments);
    }
}
