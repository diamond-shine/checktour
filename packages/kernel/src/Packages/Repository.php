<?php

namespace Shelter\Kernel\Packages;

class Repository
{
    /**
     * @var Package[]
     */
    protected $packages;

    /**
     * Repository constructor.
     */
    public function __construct()
    {
        $this->packages = [];
    }

    /**
     * @param Package $package
     * @return $this
     */
    public function register(Package $package): Repository
    {
        $this->packages[$package->fullName()] = $package;

        return $this;
    }

    /**
     * @param string $name
     * @return null|Package
     */
    public function get(string $name): ?Package
    {
        return $this->packages[$name] ?? null;
    }

    /**
     * @return Package[]
     */
    public function all(): array
    {
        return $this->packages;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->packages[$name]);
    }
}
