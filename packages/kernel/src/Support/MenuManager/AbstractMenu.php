<?php

namespace Shelter\Kernel\Support\MenuManager;

use Illuminate\Support\Str;
use Shelter\Kernel\Support\NestedTree\Item as BaseItem;

abstract class AbstractMenu
{
    /**
     * @var BaseItem
     */
    protected $cached;

    /**
     * @return string
     */
    public function name(): string
    {
        return Str::snake(
            class_basename(static::class),
            '-'
        );
    }

    /**
     * @return Item|BaseItem
     */
    public function build(): BaseItem
    {
        if ($this->cached === null) {
            $this->cached = $this->resolveItem('root', 0);

            $this->builder($this->cached);
        }

        return $this->cached;
    }

    /**
     * @param string $name
     * @param float|null $weight
     * @return Item|BaseItem
     */
    public function resolveItem(string $name, float $weight = null): BaseItem
    {
        return new Item($name, $weight);
    }

    /**
     * @param BaseItem|Item $root
     */
    abstract public function builder(BaseItem $root): void;
}
