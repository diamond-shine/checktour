<?php

namespace Shelter\Guard\Providers;

use Illuminate\Foundation\Application;
use Shelter\Guard\Http\Controllers\UsersGroupsController;
use Shelter\Guard\Http\Middleware;
use Shelter\Guard\Injections\Permissions;
use Shelter\Guard\Models\{
    User,
    UserRole
};
use Shelter\Kernel\Support\AbstractServiceProvider;
use Shelter\Guard\Permissions\Manager as PermissionsManager;

class GuardServiceProvider extends AbstractServiceProvider
{
    /**
     * @return array
     */
    public static function modelsMorphMap(): array
    {
        return [
            'user' => [
                'control' => User::class,
                'front' => User::class,
            ],
            'users_role' => [
                'control' => UserRole::class,
                'front' => UserRole::class,
            ],
        ];
    }

    /**
     * @return void
     */
    public function registerGlobal(): void
    {
        $this->app->singleton('shelter.auth', function (Application $app) {
            return $app['auth']->guard('control');
        });

        $this->app->singleton('shelter.permissions', PermissionsManager::class);

        $this->registerMiddlewareAliases();
    }

    /**
     * @return void
     */
    protected function registerMiddlewareAliases(): void
    {
        $this->app['router']->aliasMiddleware(
            'control.guest',
            Middleware\RedirectIfAuthenticated::class
        );

        $this->app['router']->aliasMiddleware(
            'control.auth',
            Middleware\Authenticate::class
        );

        $this->app['router']->aliasMiddleware(
            'control.permission',
            Middleware\PermissionMiddleware::class
        );

        $this->app['router']->aliasMiddleware(
            'control.any-permission',
            Middleware\AnyPermissionMiddleware::class
        );

        $this->app['router']->aliasMiddleware(
            'control.role',
            Middleware\RoleMiddleware::class
        );
    }
}
