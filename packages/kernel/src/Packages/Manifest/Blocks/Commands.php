<?php

namespace Shelter\Kernel\Packages\Manifest\Blocks;

use Illuminate\Support\Arr;
use Shelter\Kernel\Packages\Package;

class Commands extends AbstractManifestBlock
{
    /**
     * @var array
     */
    public $items;

    /**
     * Commands constructor.
     * @param array $commands
     */
    public function __construct(array $commands = [])
    {
        $this->items = $commands;
    }

    /**
     * @param array $manifest
     * @param Package $package
     * @return static
     */
    public static function make(array $manifest, Package $package)
    {
        return new static(
            Arr::get($manifest, 'commands', [])
        );
    }

    /**
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }
}
