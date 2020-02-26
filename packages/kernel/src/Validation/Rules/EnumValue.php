<?php

namespace Shelter\Kernel\Validation\Rules;

class EnumValue extends EnumKey
{
    /**
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        return $this->enumClass::hasValue($value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "enum_value:{$this->enumClass}";
    }
}
