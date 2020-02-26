<?php

namespace Shelter\Kernel\Support\MenuManager;

use Shelter\Kernel\Support\NestedTree\Item as BaseItem;

class Manager
{
    /**
     * @var AbstractMenu[]
     */
    protected $menus;

    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $this->menus = [];
    }

    /**
     * @param AbstractMenu $menu
     */
    public function register(AbstractMenu $menu): void
    {
        $this->menus[$menu->name()] = $menu;
    }

    /**
     * @param string $name
     * @return BaseItem
     */
    public function get(string $name): BaseItem
    {
        if (! $this->exists($name)) {
            throw new \LogicException("Menu with name [{$name}] not found");
        }

        return $this->menus[$name]->build();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        return isset($this->menus[$name]);
    }
}
