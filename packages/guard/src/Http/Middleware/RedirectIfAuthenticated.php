<?php

namespace Shelter\Guard\Http\Middleware;

use Closure;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (app('shelter.auth')->check()) {
            $redirectUrl = control_prefix();

            $resolve = function (string $url) use (&$redirectUrl) {
                $redirectUrl = $url;
            };

            app('events')->dispatch(
                'control.resolve.redirect_if_authenticated_url',
                $resolve
            );

            return redirect()->to($redirectUrl);
        }

        return $next($request);
    }
}
