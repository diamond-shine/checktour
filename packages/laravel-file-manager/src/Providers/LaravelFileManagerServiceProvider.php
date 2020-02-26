<?php

namespace Ideil\LaravelFileManager\Providers;

use Ideil\LaravelFileManager\Manager;
use Ideil\LaravelFileManager\Models\File;
use Ideil\LaravelFileManager\Models\FileCollection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class LaravelFileManagerServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(): void
    {
        $this->registerConfigsPublish();
        $this->registerMigrationsPublish();
    }

    /**
     * @return void
     */
    protected function registerMigrationsPublish(): void
    {
        $this->loadMigrationsFrom(
            \dirname(__DIR__, 2) . '/database/migrations'
        );
    }

    /**
     * @return void
     */
    protected function registerConfigsPublish(): void
    {
        $config = \dirname(__DIR__, 2) . '/resources/configs/laravel-file-manager.php';

        $this->publishes(
            [
                $config => config_path('laravel-file-manager.php'),
            ],
            'configs'
        );

        $this->mergeConfigFrom($config, 'laravel-file-manager');
    }

    /**
     * @return void
     */
    public function register(): void
    {
        app()->singleton('file-manager', function ($app) {
            return new Manager($app);
        });

        Relation::morphMap([
            'file' => File::class,
            'file_collection' => FileCollection::class,
        ]);
    }
}
