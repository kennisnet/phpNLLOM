<?php

namespace Kennisnet\NLLOM\Library;
/**
 * LomString
 */
class LomString
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return (string) $this->value;
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}
