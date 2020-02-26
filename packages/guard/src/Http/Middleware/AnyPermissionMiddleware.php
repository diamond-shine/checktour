<?php

namespace Shelter\Guard\Http\Middleware;

use Shelter\Guard\Exceptions\UnauthorizedException;
use Closure;

class AnyPermissionMiddleware
{
    use ParsePermissions;

    /**
     * @param $request
     * @param Closure $next
     * @param $permissions
     * @return mixed
     * @throws UnauthorizedException
     */
    public function handle($request, Closure $next, ...$permissions)
    {
        if (app('shelter.auth')->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        foreach ($permissions as $permission) {
            [$permission, $args] = $this->parseArguments($permission, $request);

            if (app('shelter.auth')->user()->can($permission, $args)) {
                return $next($request);
            }
        }

        throw UnauthorizedException::forPermissions($permissions);
    }
}
