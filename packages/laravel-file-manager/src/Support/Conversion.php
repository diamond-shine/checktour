<?php

namespace Ideil\LaravelFileManager\Support;

use Ideil\LaravelFileManager\Models\File;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;

/**
 * Class Conversion
 * @package Ideil\LaravelFileManager\Support
 *
 * @method backgroundColor(string $color)
 * @method bc(string $color)
 * @method crop(string $color = null)
 * @method c(string $color = null)
 * @method watermark(string $name = null)
 * @method w(string $name = null)
 * @method fit()
 * @method f()
 */
class Conversion implements Htmlable
{
    /**
     * @var File
     */
    protected $image;

    /**
     * @var string
     */
    protected $resize;

    /**
     * @var array
     */
    protected $conversions;

    /**
     * Conversion constructor.
     * @param File $image
     * @param string $resize
     */
    public function __construct(File $image, string $resize)
    {
        $this->image = $image;
        $this->resize = $resize;
        $this->conversions = [];
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return Conversion
     */
    public function __call(string $name, array $arguments)
    {
        $name = str_replace(
            '-',
            '_',
            Str::snake($name)
        );

        if (! empty($arguments[0])) {
            $this->conversions[$name] = $arguments[0];
        } else {
            $this->conversions[] = $name;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->conversions;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return $this->image->thumb(
            $this->resize,
            $this->toArray()
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->url();
    }

    /**
     * @return string|void
     */
    public function toHtml()
    {
        return $this->url();
    }
}
