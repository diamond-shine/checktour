<?php

namespace Shelter\Kernel\Support;

use Illuminate\Contracts\Support\Htmlable;

class CustomHtml implements Htmlable
{
    /**
     * @var string
     */
    protected $html;

    /**
     * CustomHtml constructor.
     * @param string $html
     */
    public function __construct(string $html)
    {
        $this->html = $html;
    }


    /**
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->html;
    }

    /**
     * @param string $html
     * @return CustomHtml
     */
    public static function render(string $html): CustomHtml
    {
        return new static($html);
    }
}
