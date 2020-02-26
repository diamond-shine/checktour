<?php

namespace Shelter\Kernel\Support\MenuManager;

use Illuminate\Support\Arr;
use Route;
use Shelter\Kernel\Support\NestedTree\Item as BaseItem;

class Item extends BaseItem
{
    /**
     * @var array
     */
    protected $link;

    /**
     * @param string $name
     * @param mixed ...$params
     * @return $this
     */
    public function setRoute(string $name, ...$params): self
    {
        $this->link = [
            'type' => 'route',
            'payload' => [
                'name' => $name,
                'params' => Arr::flatten($params),
            ],
        ];

        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->link = [
            'type' => 'url',
            'payload' => $url,
        ];

        return $this;
    }

    /**
     * @param string $name
     * @param mixed ...$params
     * @return $this
     */
    public function setAction(string $name, ...$params): self
    {
        $this->link = [
            'type' => 'action',
            'payload' => [
                'name' => $name,
                'params' => Arr::flatten($params),
            ],
        ];

        return $this;
    }

    /**
     * @param bool $absolute
     * @return string|null
     */
    public function url(bool $absolute = true): ?string
    {
        if (! $this->link) {
            return null;
        }

        if ($this->link['type'] === 'action') {
            return action(
                $this->link['payload']['name'],
                $this->link['payload']['params'],
                $absolute
            );
        } elseif ($this->link['type'] === 'route') {
            return route(
                $this->link['payload']['name'],
                $this->link['payload']['params'],
                $absolute
            );
        }

        return $this->link['payload'];
    }

    /**
     * @param bool $withNesting
     * @return bool
     */
    public function active(bool $withNesting = true): bool
    {
        $info = \parse_url(
            $this->url()
        );

        $path = \ltrim($info['path'] ?? null, '/');

        return \request()->is($path)
            || ($withNesting ? \request()->is("{$path}/*") : false);
    }
}
