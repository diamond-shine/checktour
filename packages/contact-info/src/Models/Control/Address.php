<?php

namespace Shelter\ContactInfo\Models\Control;

use Shelter\ContactInfo\Models\Address as BaseAddress;
use Shelter\Kernel\Database\ControlModel;

/**
 * Class Address
 * @package Shelter\ContactInfo\Models\Control
 *
 * @inheritdoc
 */
class Address extends BaseAddress
{
    use ControlModel;
}
