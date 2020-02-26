<?php

namespace Shelter\Kernel\Packages\Manifest\Blocks;

use Illuminate\Support\Arr;
use Shelter\Kernel\Packages\Package;

class Settings extends AbstractManifestBlock
{
    /**
     * @var bool
     */
    public $status;

    /**
     * Assets constructor.
     * @param bool $status
     */
    public function __construct(bool $status)
    {
        $this->status = $status;
    }

    /**
     * @param array $manifest
     * @param Package $package
     * @return self
     */
    public static function make(array $manifest, Package $package): self
    {
        return new static(
            (bool)Arr::get($manifest, 'settings')
        );
    }
}
