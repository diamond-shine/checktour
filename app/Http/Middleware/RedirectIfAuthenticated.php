<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
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
