<?php

namespace Shelter\Kernel\Packages\Manifest\Blocks;

use Illuminate\Support\Arr;
use Shelter\Kernel\Packages\Package;

class HotRelations extends AbstractManifestBlock
{
    /**
     * @var array
     */
    public $items;

    /**
     * HotRelations constructor.
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * @param array $manifest
     * @param Package $package
     * @return self
     */
    public static function make(array $manifest, Package $package): self
    {
        $injectionClasses = Arr::get($manifest, 'hot_relations', []);

        return new static($injectionClasses);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }
}
