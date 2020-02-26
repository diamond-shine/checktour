<?php

namespace Ideil\ImageMiner;

trait TokenTrait
{
    use HashingTrait;

    /**
     * Make token from string.
     *
     * @param string $str
     * @param string $secret
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function tokenFromStr($str, $secret = null)
    {
        $secret = $secret ?: env('APP_KEY');

        return substr($this->str($secret . $str), 0, 32);
    }

    /**
     * Make token from string.
     *
     * @param string $str
     * @param string $secret
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function token6FromStr($str, $secret = null)
    {
        return substr($this->tokenFromStr($str, $secret), 0, 6);
    }
}
