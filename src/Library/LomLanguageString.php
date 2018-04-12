<?php
namespace Kennisnet\NLLOM\Library;
/**
 * LOM language string
 */
class LomLanguageString
{
    private $languageCode;
    private $value;

    public function __construct($value, $languageCode = '')
    {
        $this->value = $value;
        $this->languageCode = $languageCode;
    }

    public function setLanguageCode($code)
    {
        $this->languageCode = $code;
        return $this;
    }

    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    public function getValue()
    {
        return $this->value;
    }

}
