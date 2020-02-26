<?php

namespace Shelter\ContactInfo\Models\Front;

use Shelter\ContactInfo\Models\Telephone as BaseTelephone;
use Shelter\Kernel\Database\FrontModel;

/**
 * Class Telephone
 * @package Shelter\ContactInfo\Models\Front
 *
 * @inheritdoc
 */
class Telephone extends BaseTelephone
{
    use FrontModel;
}
