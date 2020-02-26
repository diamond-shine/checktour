<?php

namespace Shelter\Kernel\Packages\Manifest\Blocks;

use Shelter\Kernel\Exceptions\ManifestStructureException;
use Shelter\Kernel\Packages\Manifest\Blocks\Database\Migrations;
use Shelter\Kernel\Packages\Package;

class Database extends AbstractManifestBlock
{
    /**
     * @var Migrations|null
     */
    public $migrations;

    /**
     * Assets constructor.
     * @param Migrations|null $migrations
     */
    public function __construct(Migrations $migrations = null)
    {
        $this->migrations = $migrations;
    }

    /**
     * @param array $manifest
     * @param Package $package
     * @return static|null
     * @throws ManifestStructureException
     */
    public static function make(array $manifest, Package $package)
    {
        return new static(
            Migrations::create($manifest, $package)
        );
    }
}