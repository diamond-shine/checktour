<?php

namespace Shelter\Kernel\Packages\Manifest\Blocks;

use Illuminate\Support\Arr;
use Shelter\Kernel\Packages\Package;

class Configs extends AbstractManifestBlock
{
    /**
     * @var string
     */
    public $items;

    /**
     * Assets constructor.
     */
    public function __construct()
    {
        $this->items = [];
    }

    /**
     * @param array $manifest
     * @param Package $package
     * @return static
     */
    public static function make(array $manifest, Package $package): Configs
    {
        $items = Arr::get($manifest, 'configs');

        $block = new static;

        if (! $items) {
            return $block;
        }

        foreach ($items as $filename => $path) {
            $block->add($filename, $path);
        }

        return $block;
    }

    /**
     * @param string $filename
     * @param string $path
     * @return Configs
     */
    public function add(string $filename, string $path): Configs
    {
        $this->items[$filename] = $path;

        return $this;
    }
}
