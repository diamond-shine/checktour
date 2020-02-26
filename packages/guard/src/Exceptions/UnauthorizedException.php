<?php

namespace Shelter\Guard\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedException extends HttpException
{
    /**
     * @var array
     */
    private $requiredRoles = [];

    /**
     * @var array
     */
    private $requiredPermissions = [];

    /**
     * @param array $roles
     * @return UnauthorizedException
     */
    public static function forRoles(array $roles): self
    {
        $message = 'User does not have the right roles.';

        if (config('shelter.guard.display_permission_in_exception')) {
            $message = 'User does not have the right roles. Necessary roles are '
                . implode(', ', $roles);
        }

        $exception = new static(403, $message, null, []);
        $exception->requiredRoles = $roles;

        return $exception;
    }

    /**
     * @param array $permissions
     * @return UnauthorizedException
     */
    public static function forPermissions(array $permissions): self
    {
        $message = 'User does not have the right permissions.';

        if (config('shelter.guard.display_permission_in_exception')) {
            $cleanPermissions = array_map(function (string $permission) {
                return \explode('?', $permission)[0];
            }, $permissions);

            $message = 'User does not have the right permissions. Necessary permissions are '
                . implode(', ', $cleanPermissions);
        }

        $exception = new static(403, $message, null, []);
        $exception->requiredPermissions = $permissions;

        return $exception;
    }

    /**
     * @return UnauthorizedException
     */
    public static function notLoggedIn(): self
    {
        return new static(403, 'User is not logged in.', null, []);
    }

    /**
     * @return array
     */
    public function getRequiredRoles(): array
    {
        return $this->requiredRoles;
    }

    /**
     * @return array
     */
    public function getRequiredPermissions(): array
    {
        return $this->requiredPermissions;
    }
}
