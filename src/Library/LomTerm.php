<?php

namespace Kennisnet\NLLOM\Library;
/**
 * LomTerm
 */
class LomTerm
{
    private $source;
    private $value;

    public function __construct($value, $source = 'LOMv1.0')
    {
        $this->source = $source;
        $this->value = $value;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getValue()
    {
        return $this->value;
    }
}
