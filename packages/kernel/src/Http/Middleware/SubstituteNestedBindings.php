<?php

namespace Shelter\Kernel\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;

class SubstituteNestedBindings
{
    /**
     * @var array
     */
    protected static $nestedBindings = [];

    /**
     * The router instance.
     *
     * @var Registrar
     */
    protected $router;

    /**
     * Create a new bindings substitutor.
     *
     * @param Registrar $router
     * @return void
     */
    public function __construct(Registrar $router)
    {
        $this->router = $router;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();
        $parameters = $route->originalParameters();

        $keyParts = [];

        foreach ($parameters as $name => $value) {
            $keyParts[] = \str_replace('__', '', $name);

            if (! Str::startsWith($name, '__')) {
                continue;
            }

            $route->setParameter(
                $name,
                $this->resolveNestedParameter(
                    $keyParts,
                    $value,
                    $route->parameters(),
                    $route
                )
            );
        }

        return $next($request);
    }

    /**
     * @param array $keyParts
     * @param string $value
     * @param array $parameters
     * @param Route $route
     * @return mixed
     */
    public function resolveNestedParameter(array $keyParts, $value, array $parameters, Route $route)
    {
        $key = \implode('.', $keyParts);

        if (! isset(static::$nestedBindings[$key])) {
            return $value;
        }

        \array_pop($keyParts);

        $arguments = [];

        foreach ($keyParts as $index => $keyPart) {
            $arguments[] = $parameters[$index !== 0 ? "__{$keyPart}" : $keyPart];
        }

        $currentParameters = \array_merge([$value], $arguments, [$route]);

        return \call_user_func_array(
            static::$nestedBindings[$key],
            $currentParameters
        );
    }

    /**
     * @param string $key
     * @param Closure $resolver
     */
    public static function addNestedBind(string $key, Closure $resolver): void
    {
        static::$nestedBindings[$key] = $resolver;
    }
}
