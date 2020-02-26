<?php

namespace Shelter\Guard\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait ParsePermissions
{
    /**
     * @param string $permission
     * @param Request $request
     * @return array
     */
    protected function parseArguments(string $permission, Request $request): array
    {
        if (! Str::contains($permission, '?')) {
            return [
                $permission,
                [],
            ];
        }

        [$permission, $argsString] = \explode('?', $permission);

        $params = \explode('&', $argsString);

        $args = [];

        foreach ($params as $param) {
            if (! Str::contains($param, '.')) {
                $args[] = $request->route($param);
            } else {
                [$param, $query] = \explode('.', $param, 2);

                $args[] = data_get(
                    $request->route($param),
                    $query
                );
            }
        }

        return [$permission, $args];
    }
}