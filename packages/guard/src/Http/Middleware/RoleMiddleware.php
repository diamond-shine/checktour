<?php

namespace Shelter\Guard\Http\Middleware;

use Shelter\Guard\Exceptions\UnauthorizedException;
use Closure;

class RoleMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @param array $roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        if (app('shelter.auth')->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        if (app('shelter.auth')->user()->isAdmin()) {
            return $next($request);
        }

        if (app('shelter.auth')->user()->hasAllRoles($roles)) {
            return $next($request);
        }

        throw UnauthorizedException::forRoles($roles);
    }
}
