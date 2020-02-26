<?php

namespace Shelter\Kernel\Validation\Rules;

class EnumKey
{
    /**
     * @var string
     */
    protected $enumClass;

    /**
     * UniqueSlug constructor.
     * @param string $enumClass
     */
    public function __construct(string $enumClass)
    {
        $this->enumClass = $enumClass;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function passes($value): bool
    {
        return $this->enumClass::hasKey($value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "enum_key:{$this->enumClass}";
    }

    /**
     * @param string $enumClass
     * @return $this
     */
    public static function make(string $enumClass): self
    {
        return new static($enumClass);
    }
}
