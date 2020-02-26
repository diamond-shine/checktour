<?php

namespace Shelter\Guard\Http\Middleware;

use Shelter\Guard\Exceptions\UnauthorizedException;
use Closure;

class PermissionMiddleware
{
    use ParsePermissions;

    /**
     * @param $request
     * @param Closure $next
     * @param array $permissions
     * @return mixed
     */
    public function handle($request, Closure $next, ...$permissions)
    {
        if (app('shelter.auth')->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        foreach ($permissions as $permission) {
            [$permission, $args] = $this->parseArguments($permission, $request);

            if (! app('shelter.auth')->user()->can($permission, $args)) {
                throw UnauthorizedException::forPermissions($permissions);
            }
        }

        return $next($request);
    }
}
