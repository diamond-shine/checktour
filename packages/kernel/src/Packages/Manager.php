<?php

namespace Shelter\Kernel\Packages;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;

class Manager
{
    /**
     * @var array
     */
    protected $packages;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * Manager constructor.
     */
    public function __construct()
    {
        $this->repository = new Repository;
    }

    /**
     * @return Repository
     */
    public function repository(): Repository
    {
        return $this->repository;
    }

    /**
     * @param string $type
     * @return null|string
     */
    public function findInMorphMap(string $type): ?string
    {
        return Arr::get(
            Relation::morphMap(),
            $type
        );
    }

    /**
     * @param string $targetClass
     * @return null|string
     */
    public function findInMorphMapByClass(string $targetClass): ?string
    {
        foreach (Relation::morphMap() as $type => $class) {
            if ($class === $targetClass) {
                return $type;
            }
        }

        return null;
    }
}
