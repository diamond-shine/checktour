<?php

namespace Shelter\Kernel\Packages\Manifest\Blocks;

use Shelter\Kernel\Exceptions\ManifestStructureException;
use Shelter\Kernel\Packages\Manifest\ManifestBlockInterface;
use Shelter\Kernel\Packages\Package;

abstract class AbstractManifestBlock implements ManifestBlockInterface
{
    /**
     * @param array $manifest
     * @param Package $package
     * @return null|AbstractManifestBlock
     * @throws ManifestStructureException
     */
    public static function create(array $manifest, Package $package)
    {
        try {
            return static::make($manifest, $package);
        } catch (\Throwable $e) {
            throw new ManifestStructureException(
                $e->getMessage() . '. In package [' . $package->fullName() . ']'
            );
        }
    }
}