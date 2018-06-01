<?php

namespace Kennisnet\NLLOM\Library;

class LomDateTime
{
    private $dateTime;
    private $dateTimeDescription;

    public function __construct(
        LomString $dateTime,
        LomMultiLanguage $dateTimeDescription = null
    ) {
        $this->dateTime = $dateTime;
        $this->dateTimeDescription = $dateTimeDescription;
    }

    /**
     * @return LomString
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * gets the datetime description
     * @return LomMultiLanguage description (Dutch if available, otherwise another value) or NULL if not set
     */
    public function getDateTimeDescription()
    {
        return $this->dateTimeDescription;
    }

}
