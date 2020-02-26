<?php

namespace Shelter\Kernel\Support\NestedTree;

use ArrayIterator;
use Illuminate\Support\Collection;
use IteratorAggregate;
use Traversable;

class Item implements IteratorAggregate
{
    /**
     * @var float
     */
    public $weight;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Collection
     */
    protected $children;

    /**
     * @var Collection
     */
    protected $props;

    /**
     * @var int
     */
    protected $depth;

    /**
     * @var Item|null
     */
    protected $parent;

    /**
     * Sidebar constructor.
     * @param string $name
     * @param float $weight
     */
    public function __construct(string $name, float $weight = 10)
    {
        $this->name = $name;
        $this->weight = $weight;

        $this->depth = 0;

        $this->children = Collection::make();
        $this->props = Collection::make();
    }

    /**
     * @param string $name
     * @param float $weight
     * @return $this
     */
    public function item(string $name, float $weight = 10): self
    {
        $item = $this->children->first->is($name);

        if (! $item) {
            $itemClass = $this->resolveItemClass();

            $item = new $itemClass($name, $weight);

            $item->setDepth(
                $this->getDepth() + 1
            );

            $item->setParent($this);

            $this->children->push($item);
            $this->children = $this
                ->children
                ->sortBy('weight')
                ->values();
        }

        return $item;
    }

    /**
     * @return string
     */
    protected function resolveItemClass(): string
    {
        return static::class;
    }

    /**
     * @return int
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * @param int $depth
     */
    public function setDepth(int $depth): void
    {
        $this->depth = $depth;
    }

    /**
     * @return Item|null
     */
    public function nextSibling(): ?Item
    {
        return $this->getSiblings()->get(
            $this->getPosition() + 1
        );
    }

    /**
     * @return Collection
     */
    public function getSiblings(): Collection
    {
        return $this->getParent() ?
            $this->getParent()->children() :
            Collection::make();
    }

    /**
     * @return Item|null
     */
    public function getParent(): ?Item
    {
        return $this->parent;
    }

    /**
     * @param Item $parent
     */
    public function setParent(Item $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return Collection
     */
    public function children(): Collection
    {
        return $this->children;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        if (! $this->getParent()) {
            return 0;
        }

        return $this->getParent()->childrenIds()->search(
            $this->getName()
        ) ?: 0;
    }

    /**
     * @return Collection
     */
    public function childrenIds(): Collection
    {
        return $this->children()->map->getName();
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this|mixed
     */
    public function prop(string $name)
    {
        if (\func_num_args() === 2) {
            $this->props->put(
                $name,
                \func_get_arg(1)
            );

            return $this;
        }

        return $this->props->get($name);
    }

    /**
     * @return Collection
     */
    public function mapToTree(): Collection
    {
        $sortedChildren = $this
            ->children
            ->values()
            ->sortBy('weight');

        return collect([
            'key' => $this->key(),
            'name' => $this->name,
            'props' => $this->props,
            'weight' => $this->weight,
            'parentKey' => $this->getParent() ?
                $this->getParent()->key() :
                null,
            'children' => $sortedChildren
                ->map
                ->mapToTree(),
            '_lft' => $this->getLft(),
            '_rgt' => $this->getRgt(),
        ]);
    }

    /**
     * @return string
     */
    public function key(): string
    {
        return $this->getLft() . ':' . $this->getRgt();
    }

    /**
     * @return int
     */
    public function getLft(): int
    {
        if ($prev = $this->prevSibling()) {
            return $prev->getRgt() + 1;
        }

        if ($this->parent) {
            return $this->parent->getLft() + 1;
        }

        return 1;
    }

    /**
     * @return Item|null
     */
    public function prevSibling(): ?Item
    {
        return $this->getSiblings()->get(
            $this->getPosition() - 1
        );
    }

    /**
     * @return int
     */
    public function getRgt(): int
    {
        return $this->getLft() + $this->ancestorsCount() * 2 + 1;
    }

    /**
     * @return int
     */
    public function ancestorsCount(): int
    {
        return $this->children()->reduce(function ($sum, Item $item) {
            return $sum + $item->ancestorsCount();
        }, $this->children()->count());
    }

    /**
     * @return Collection
     */
    public function map(): Collection
    {
        $sortedChildren = $this
            ->children
            ->values()
            ->sortBy('weight');

        return collect([
            'key' => $this->key(),
            'name' => $this->name,
            'props' => $this->props,
            'weight' => $this->weight,
            'depth' => $this->getDepth(),
            'parentKey' => $this->getParent() ?
                $this->getParent()->key() :
                null,
            'children' => $sortedChildren->map->getName(),
            '_lft' => $this->getLft(),
            '_rgt' => $this->getRgt(),
        ]);
    }

    /**
     * @return Collection
     */
    public function mapToFlatTree(): Collection
    {
        return $this->toFlatTree()->map->map();
    }

    /**
     * @return Collection
     */
    public function toFlatTree(): Collection
    {
        $iteration = function (Item $item, Collection $items = null) use (&$iteration) {
            if ($items === null) {
                $items = collect()->push($item);
            }

            if (! $item->hasChildren()) {
                return $items;
            }

            foreach ($item->children() as $child) {
                $items = $iteration($child, $items);
            }

            return $items->merge(
                $item->children()
            );
        };

        $flatTree = $iteration($this);

        return $flatTree->sortBy->getLft()->values();
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->children()->isNotEmpty();
    }

    /**
     * @param string $name
     * @return bool
     */
    public function is(string $name): bool
    {
        return $this->name === $name;
    }

    /**
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->parent === null;
    }

    /**
     * @return array|Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator(
            $this->children()->all()
        );
    }
}
