<?php

namespace Shelter\Guard\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * Authenticate constructor.
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! app('shelter.auth')->check()) {
            $redirectUrl = route('login-page');

            $resolve = function (string $url) use (&$redirectUrl) {
                $redirectUrl = $url;
            };

            app('events')->dispatch(
                'control.resolve.unauthenticated_url',
                $resolve
            );

            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->guest($redirectUrl);
        }

        return $next($request);
    }
}
