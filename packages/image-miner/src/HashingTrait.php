<?php

namespace Ideil\ImageMiner;

use Base2n;
use InvalidArgumentException;

trait HashingTrait
{

    /**
     * @var integer
     */
    protected $hash_alphabet_bits_per_char = 5;

    /**
     * length must be 2^hash_alphabet_bits_per_char
     * @var string
     */
    protected $hash_alphabet = 'abcdefghijklmnopqrstuvwxyz234567';

    /**
     * Change base
     *
     * @param  binary $binary
     * @return string
     * @throws InvalidArgumentException
     */
    public function base($binary)
    {
        $base = new Base2n(
            $this->hash_alphabet_bits_per_char,
            $this->hash_alphabet,
            true,
            true,
            true
        );

        return $base->encode($binary);
    }

    /**
     * Make hash from string.
     *
     * @param  string $str
     * @return string
     * @throws InvalidArgumentException
     */
    public function str($str)
    {
        return $this->base(hash('sha256', $str, true));
    }

}
