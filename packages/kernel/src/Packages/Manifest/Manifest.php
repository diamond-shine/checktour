<?php

namespace Shelter\Kernel\Packages\Manifest;

use Shelter\Kernel\Packages\Manifest\Blocks\{
    AbstractManifestBlock,
    Alias,
    Assets,
    Commands,
    Configs,
    Database,
    HotRelations,
    Routes,
    Settings
};
use Exception;
use ReflectionException;
use Shelter\Kernel\Packages\Package;
use Shelter\Kernel\Packages\Manifest\Factory as ManifestFactory;

/**
 * Class Manifest
 * @package Shelter\Kernel\Packages
 *
 * @property Database $database
 * @property Configs $configs
 * @property Settings $settings
 * @property Commands $commands
 * @property HotRelations $hotRelations
 */
class Manifest
{
    /**
     * @var array
     */
    private static $blockParsers = [
        'database' => Database::class,
        'configs' => Configs::class,
        'settings' => Settings::class,
        'commands' => Commands::class,
        'hotRelations' => HotRelations::class,
    ];

    /**
     * @var AbstractManifestBlock[]
     */
    protected $blocks;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Package
     */
    protected $package;

    /**
     * Manifest constructor.
     * @param Package $package
     * @throws ReflectionException
     */
    public function __construct(Package $package)
    {
        $this->package = $package;

        $this->config = $this->resolveConfig();

        $this->blocks = [];
    }

    /**
     * @param string $message
     * @throws \LogicException
     */
    protected function assert(string $message): void
    {
        throw new \LogicException($message);
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    protected function resolveConfig()
    {
        $path = $this->package->path('manifest.php');

        if (! file_exists($path)) {
            $this->assert("Manifest file not found in package [{$this->package->fullName()}]");
        }

        $factory = ManifestFactory::make();

        require $path;

        return $factory->toArray();
    }

    /**
     * @param string $name
     * @return ManifestBlockInterface
     * @throws Exception
     */
    public function __get(string $name): ManifestBlockInterface
    {
        if (! isset(static::$blockParsers[$name])) {
            throw new Exception("Manifest does not have block with name [$name]");
        }

        if (! array_key_exists($name, $this->blocks)) {
            $this->blocks[$name] = static::$blockParsers[$name]::create($this->config, $this->package);
        }

        return $this->blocks[$name];
    }
}
