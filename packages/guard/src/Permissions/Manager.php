<?php

namespace Shelter\Guard\Permissions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Manager
{
    /**
     * @var PermissionInterface[]
     */
    protected $bindings;

    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $this->bindings = [];
    }

    /**
     * @param PermissionInterface $permission
     */
    public function bind(PermissionInterface $permission): void
    {
        foreach ($permission->binders() as $name => $handler) {
            $this->bindings[$name] = \is_callable($handler) ?
                $handler :
                [$permission, $handler];
        }
    }

    /**
     * @param string $permission
     * @return bool
     */
    public function has(string $permission): bool
    {
        return isset($this->bindings[$permission]);
    }

    /**
     * @param Permissionable $entity
     * @param string $permission
     * @param array $arguments
     * @return bool
     */
    public function can(Permissionable $entity, string $permission, array $arguments = []): bool
    {
        if ($entity->isAdmin()
            || $entity->allPermissions()->contains($permission)
        ) {
            return true;
        }

        $variant = null;

        if (Str::contains($permission, '#')) {
            [$permission, $variant] = \explode('#', $permission);
        }

        if (! $this->has($permission)) {
            return false;
        }

        return $this->bindings[$permission]($entity, $arguments, $variant);
    }
}
