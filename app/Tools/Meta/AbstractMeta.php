<?php

namespace App\Tools\Meta;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

abstract class AbstractMeta implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * @return string
     */
    abstract public function type(): string;

    /**
     * @return array
     */
    abstract public function payload(): array;

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'payload' => $this->payload(),
        ];
    }

    /**
     * @param mixed ...$args
     * @return static
     */
    public static function make(...$args)
    {
        return new static(...$args);
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return \json_encode(
            $this->toArray()
        );
    }

    /**
     * @return mixed|string
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
