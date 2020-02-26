<?php

namespace Shelter\Kernel\Packages;

use ReflectionException;
use Shelter\Kernel\Packages\Manifest\Manifest;
use Shelter\Kernel\Support\AbstractServiceProvider;

class Package
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $vendor;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var AbstractServiceProvider
     */
    protected $provider;

    /**
     * @var Manifest
     */
    protected $manifest;

    /**
     * @var array|null
     */
    protected $settings;

    /**
     * Package constructor.
     * @param AbstractServiceProvider $provider
     */
    public function __construct(AbstractServiceProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return Manifest
     * @throws ReflectionException
     */
    public function getManifest(): Manifest
    {
        if ($this->manifest === null) {
            $this->manifest = new Manifest($this);
        }

        return $this->manifest;
    }

    /**
     * @return string
     */
    public function fullName(): string
    {
        return $this->getVendor() . '/' . $this->getName();
    }

    /**
     * @return string
     */
    public function getVendor(): string
    {
        if ($this->vendor === null) {
            $this->vendor = strtolower(
                explode('\\', \get_class($this->provider))[0]
            );
        }

        return $this->vendor;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        if ($this->name === null) {
            $this->name = \str_replace(
                '_',
                '-',
                \real_snake_case(
                    last(
                        \explode(
                            '\\',
                            $this->getNamespace()
                        )
                    )
                )
            );
        }

        return $this->name;
    }

    /**
     * @param string|null $className
     * @return string
     */
    public function getNamespace(string $className = null): string
    {
        if ($this->namespace === null) {
            $this->namespace = \implode('\\',
                \array_slice(
                    \explode(
                        '\\',
                        \get_class($this->provider)
                    ),
                    0,
                    2
                )
            );
        }

        return $className ? "\\{$this->namespace}\\$className" : $this->namespace;
    }

    /**
     * @return AbstractServiceProvider
     */
    public function getProvider(): AbstractServiceProvider
    {
        return $this->provider;
    }

    /**
     * @param string|null $path
     * @return string
     * @throws ReflectionException
     */
    public function path(string $path = null): string
    {
        return \rtrim(
            \implode('/', [
                $this->provider->getRootPath(),
                \rtrim($path, '/'),
            ]),
            '/'
        );
    }

    /**
     * @param array $data
     */
    public function registerSettings(array $data): void
    {
        $this->settings = $data;
    }

    /**
     * @return array|null
     */
    public function getSettings(): ?array
    {
        return $this->settings;
    }
}
