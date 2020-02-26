<?php

namespace Shelter\Kernel\Support;

use Illuminate\Support\ServiceProvider;
use Shelter\Kernel\Injections\Manager as InjectionsManager;
use Shelter\Kernel\Packages\Manifest\Manifest;
use Shelter\Kernel\Packages\Package;
use Shelter\Kernel\Packages\Traits\{
    RegisterCommandsTrait,
    RegisterConfigsTrait,
    RegisterHotRelationsTrait,
    RegisterSettingsTrait
};
use Shelter\Kernel\Sidebar\Item;
use Symfony\Component\Console\Exception\LogicException;

/**
 * Class AbstractServiceProvider
 * @package Shelter\Kernel\Support
 *
 * @property Package $package
 */
abstract class AbstractServiceProvider extends ServiceProvider
{
    use RegisterConfigsTrait, RegisterSettingsTrait, RegisterCommandsTrait, RegisterHotRelationsTrait;

    /**
     * @var Package
     */
    protected $package;

    /**
     * @var string
     */
    protected $rootPath;

    /**
     * @return array
     */
    public static function modelsMorphMap(): array
    {
        return [];
    }


    /**
     * @throws \LogicException
     * @throws \Symfony\Component\Console\Exception\LogicException
     * @throws \ReflectionException
     */
    public function register(): void
    {
        if (! $this->app->offsetExists('shelter')) {
            throw new LogicException('Kernel not loaded');
        }

        $this->app['shelter.packages']->repository()->register(
            $this->package = new Package($this)
        );

        if (\is_control_panel()) {
            $this->registerControl();
        }

        if (\is_front()) {
            $this->registerFront();
        }

        $this->registerGlobal();

        $this->bootTraits();

        $this->registerMigrations();
    }

    /**
     * @return void
     */
    public function registerControl(): void
    {
        //
    }

    /**
     * @return void
     */
    public function registerFront(): void
    {
        //
    }

    /**
     * @return void
     */
    public function registerGlobal(): void
    {
        //
    }

    /**
     * @return void
     */
    protected function bootTraits(): void
    {
        foreach (\class_uses_recursive(static::class) as $trait) {
            if (method_exists($this, $method = 'boot' . \class_basename($trait))) {
                $this->app->call([$this, $method]);
            }
        }
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        if (\is_control_panel()) {
            $this->bootControl();
        }

        if (\is_front()) {
            $this->bootFront();
        }

        $this->bootGlobal();
    }

    /**
     * @return void
     */
    public function bootControl(): void
    {
        //
    }

    /**
     * @return void
     */
    public function bootFront(): void
    {
        //
    }

    /**
     * @return void
     */
    public function bootGlobal(): void
    {
        //
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function getRootPath(): string
    {
        if (! $this->rootPath) {
            $info = new \ReflectionClass($this);

            $this->rootPath = preg_replace(
                '~/src/.+~',
                '',
                $info->getFileName()
            );
        }

        return $this->rootPath;
    }

    /**
     * @return InjectionsManager
     */
    public function injectionsManager(): InjectionsManager
    {
        return $this->app['shelter.injections'];
    }

    /**
     * @throws \LogicException
     * @throws \ReflectionException
     */
    protected function registerMigrations(): void
    {
        $manifest = $this->package->getManifest();

        if (! $manifest->database->migrations) {
            return;
        }

        $this->loadMigrationsFrom(
            $manifest->database->migrations->path
        );
    }

    /**
     * @return Manifest
     * @throws \ReflectionException
     */
    public function manifest(): Manifest
    {
        return $this->package->getManifest();
    }
}
