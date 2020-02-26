<?php

namespace Shelter\Guard\Permissions;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection as SupportCollection;
use Shelter\Guard\Models\UserRole;

interface Permissionable
{
    /**
     * @return bool
     */
    public function isAdmin(): bool;

    /**
     * @return MorphMany
     */
    public function permissions(): MorphMany;

    /**
     * @param mixed ...$roles
     * @return bool
     */
    public function hasAnyRole(...$roles): bool;

    /**
     * @param string|UserRole $role
     * @return bool
     */
    public function hasRole($role): bool;

    /**
     * @return SupportCollection
     */
    public function allPermissions(): SupportCollection;

    /**
     * @param mixed ...$roles
     * @return bool
     */
    public function hasAllRoles(...$roles): bool;

    /**
     * @param string $ability
     * @param array $arguments
     * @return bool
     */
    public function can($ability, $arguments = []): bool;

    /**
     * @param array $abilities
     * @return bool
     */
    public function canAll(array $abilities): bool;

    /**
     * @param array $abilities
     * @return bool
     */
    public function canAny(array $abilities): bool;

    /**
     * @param string $ability
     * @param array $arguments
     * @return bool
     */
    public function cannot($ability, $arguments = []): bool;

    /**
     * @param string $ability
     * @param array $arguments
     * @return bool
     */
    public function cant($ability, $arguments = []): bool;
}