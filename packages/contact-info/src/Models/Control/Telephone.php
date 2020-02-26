<?php

namespace Shelter\ContactInfo\Models\Control;

use Shelter\ContactInfo\Models\Telephone as BaseTelephone;
use Shelter\Kernel\Database\ControlModel;

/**
 * Class Telephone
 * @package Shelter\ContactInfo\Models\Control
 *
 * @inheritdoc
 */
class Telephone extends BaseTelephone
{
    use ControlModel;
}
