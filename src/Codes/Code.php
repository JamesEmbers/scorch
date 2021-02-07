<?php

namespace Cratespace\Sentinel\Codes;

abstract class Code
{
    /**
     * Generate a new and unique code.
     *
     * @return string
     */
    abstract public static function generate(): string;
}
