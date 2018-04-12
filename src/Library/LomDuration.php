<?php

namespace Kennisnet\NLLOM\Library;

class LomDuration
{
    private $duration;
    private $dateTimeDescription;

    public function __construct(
        LomInterval $duration,
        LomMultiLanguage $dateTimeDescription = null
    ) {
        $this->duration = $duration;
        $this->dateTimeDescription = $dateTimeDescription;
    }

    /**
     * @return LomInterval
     */
    public function getDuration()
    {
        return $this->duration;
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

