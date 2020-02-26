<?php

namespace Shelter\Kernel\Packages\Manifest\Blocks\Database;

use Illuminate\Support\Arr;
use Shelter\Kernel\Exceptions\ManifestStructureException;
use Shelter\Kernel\Packages\Manifest\Blocks\AbstractManifestBlock;
use Shelter\Kernel\Packages\Package;

class Migrations extends AbstractManifestBlock
{
    /**
     * @var string
     */
    public $path;

    /**
     * Assets constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @param array $manifest
     * @param Package $package
     * @return static|null
     * @throws ManifestStructureException
     * @throws \ReflectionException
     */
    public static function make(array $manifest, Package $package)
    {
        $enabled = Arr::get($manifest, 'database.migrations', false);

        if (! $enabled) {
            return null;
        }

        $path = $package->path('database/migrations');

        if (! is_dir($path)) {
            throw new ManifestStructureException('Invalid migrations path in package');
        }

        return new static($path);
    }
}
