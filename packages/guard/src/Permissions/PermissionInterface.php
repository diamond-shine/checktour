<?php

namespace Shelter\Guard\Permissions;

/**
 * Interface PermissionInterface
 * @package Shelter\Guard\Permissions
 */
interface PermissionInterface
{
    /**
     * @return array
     */
    public function binders(): array;
}
