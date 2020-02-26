<?php

namespace Shelter\Kernel\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\{
    AliasLoader,
    Application
};
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Kalnoy\Nestedset\NestedSet;
use Shelter\Kernel\Console;
use Shelter\Kernel\Injections\Manager as InjectionsManager;
use Shelter\Kernel\Kernel;
use Shelter\Kernel\Middleware\SubstituteNestedBindings;
use Shelter\Kernel\Packages\Manager as PackageManager;
use Shelter\Kernel\Support\MenuManager\Manager as MenuManager;

use Shelter\Kernel\Validation\Rules\{
    EnumKey,
    EnumValue
};

use Ramsey\Uuid\{
    Codec\OrderedTimeCodec,
    Uuid,
    UuidFactory
};

use Illuminate\Database\Eloquent\{
    Relations\HasMany,
    Relations\HasOne,
    Relations\Relation
};

class KernelServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     * @throws InvalidArgumentException
     */
    public function register(): void
    {
        $this->app->singleton('shelter', function (Application $app) {
            return new Kernel($app);
        });

        $this->app->singleton('shelter.menu-manager', MenuManager::class);

        $this->app->singleton('shelter.packages', PackageManager::class);

        $this->app->singleton('shelter.injections', InjectionsManager::class);

        $this->registerInstalled();

        $this->registerConsoleCommands();

        $this->registerMacros();

        $this->loadViewsFrom(
            \dirname(__DIR__, 2) . '/resources/stub',
            'spm/stub'
        );

        $this->app['router']->macro('nestedBind', function (string $key, \Closure $resolver) {
            SubstituteNestedBindings::addNestedBind($key, $resolver);
        });
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function registerInstalled(): void
    {
        $manifestPath = storage_path('app/shelter/package-manifest.php');

        if (! is_file($manifestPath)) {
            return;
        }

        $installed = require $manifestPath;

        $providers = [];
        $aliases = [];

        foreach ($installed as $module) {
            if ($module['providers'] ?? null) {
                $providers[] = $module['providers'];
            }

            if ($module['aliases'] ?? null) {
                $aliases[] = $module['aliases'];
            }
        }

        if ($providers) {
            $morphMap = [];

            foreach (\array_merge(...$providers) as $provider) {
                $this->app->register($provider);

                if ($types = $provider::modelsMorphMap()) {
                    $morphMap[] = $types;
                }
            }

            if ($morphMap) {
                $this->registerMorphMap($morphMap);
            }
        }

        if ($aliases) {
            foreach (\array_merge(...$aliases) as $alias => $facade) {
                AliasLoader::getInstance()->alias($alias, $facade);
            }
        }
    }

    /**
     * @param array $morphMap
     */
    protected function registerMorphMap(array $morphMap): void
    {
        $result = [];

        foreach (\array_merge(...$morphMap) as $name => $mapItem) {
            if (isset($mapItem['control'])) {
                $result['control'][$name] = $mapItem['control'];
            }

            if (isset($mapItem['front'])) {
                $result['front'][$name] = $mapItem['front'];
            }
        }

        if ($result) {
            Relation::morphMap(
                $result[Kernel::context()]
            );
            Relation::morphMap($result);
        }
    }

    /**
     * @return void
     */
    protected function registerConsoleCommands(): void
    {
        $this->commands([
            Console\Commands\Spm\Discover::class,
            Console\Commands\Spm\MakePackage::class,
            Console\Commands\Spm\MakeMigration::class,
            Console\Commands\Spm\MakeModel::class,
            Console\Commands\Spm\MakeHotRelation::class,
            Console\Commands\Spm\Install::class,
            Console\Commands\Spm\Remove::class,
        ]);
    }

    /**
     * @return void
     */
    protected function registerMacros(): void
    {
        Blueprint::macro(
            'typedMorphs',
            function (string $name, string $type, string $indexName = null, string $idField = 'id', bool $nullable = false) {
                if ($nullable) {
                    $this->string("{$name}_type")->nullable();
                    $this->{$type}("{$name}_{$idField}")->nullable();
                } else {
                    $this->string("{$name}_type");
                    $this->{$type}("{$name}_{$idField}");
                }

                $this->index(["{$name}_type", "{$name}_{$idField}"], $indexName);
            }
        );

        Blueprint::macro(
            'typedNestedSet',
            function (string $parentKeyType) {
                $this->unsignedInteger(NestedSet::LFT)->default(0);
                $this->unsignedInteger(NestedSet::RGT)->default(0);
                $this->{$parentKeyType}(NestedSet::PARENT_ID)->nullable();

                $this->index(
                    NestedSet::getDefaultColumns()
                );
            }
        );

        HasMany::macro('toHasOne', function () {
            return new HasOne(
                $this->getQuery(),
                $this->getParent(),
                $this->foreignKey,
                $this->localKey
            );
        });
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->optimizeForUUID();

        \Auth::macro('controlGuard', function () {
            return app('auth')->guard('general');
        });

        $this->app['validator']->extend(
            'enum_key',
            function ($attribute, $value, $parameters, $validator) {
                return EnumKey::make($parameters[0])->passes($value);
            }
        );

        $this->app['validator']->extend(
            'enum_value',
            function ($attribute, $value, $parameters, $validator) {
                return EnumValue::make($parameters[0])->passes($value);
            }
        );
    }

    /**
     * @return void
     */
    protected function optimizeForUUID(): void
    {
        $factory = new UuidFactory;

        $factory->setCodec(
            new OrderedTimeCodec(
                $factory->getUuidBuilder()
            )
        );

        Uuid::setFactory($factory);
    }
}
