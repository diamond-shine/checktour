<?php

namespace Shelter\Kernel\Packages\Manifest;

use Shelter\Kernel\Packages\Package;

interface ManifestBlockInterface
{
    /**
     * @param array $manifest
     * @param Package $package
     * @return static|null
     */
    public static function make(array $manifest, Package $package);
}