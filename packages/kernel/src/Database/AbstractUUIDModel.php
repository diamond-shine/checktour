<?php

namespace Shelter\Kernel\Database;

/**
 * Class AbstractUUIDModel
 * @package Shelter\Kernel\Database
 */
abstract class AbstractUUIDModel extends AbstractModel
{
    use ModelWithUUIDTrait;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';
}
