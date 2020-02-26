<?php

namespace Shelter\ContactInfo\Models\Front;

use Shelter\ContactInfo\Models\Address as BaseAddress;
use Shelter\Kernel\Database\FrontModel;

/**
 * Class Address
 * @package Shelter\ContactInfo\Models\Front
 *
 * @inheritdoc
 */
class Address extends BaseAddress
{
    use FrontModel;
}
