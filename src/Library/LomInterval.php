<?php

namespace Kennisnet\NLLOM\Library;

/**
 * Description of LomDuration
 *
 * @author lennaert
 */
class LomInterval
{
    const START_CODE = 'P';
    const YEARS_CODE_DUTCH = 'J';
    const YEARS_CODE_ENGLISH = 'Y';
    const MONTHS_CODE = 'M';
    const DAYS_CODE = 'D';
    const DATETIME_SEPARATOR = 'T';
    const HOURS_CODE = 'H';
    const MINUTES_CODE = 'M';
    const SECONDS_CODE = 'S';

    const SECONDS_PER_MINUTE = 60;
    const MINUTES_PER_HOUR = 60;
    const HOURS_PER_DAY = 24;
    const DAYS_PER_YEAR = 365;
    const DAYS_PER_MONTH = 30;
    const MONTHS_PER_YEAR = 12;

    private $hours;
    private $minutes;
    private $seconds;

    public function __construct($representation)
    {
        $this->parse($representation);
    }

    /**
     * parses the representation of a duration
     *
     * @param string $representation
     * @return LomInterval lom duration with one or more attributes filled, or
     *   NULL if a parsing error occurred
     */
    public function parse($representation)
    {
        $date = null;
        $time = null;

        $duration = $this;

        $matches = [];
        if (preg_match(
            '/^' . self::START_CODE . '([^' . self::DATETIME_SEPARATOR . ']+)?(' . self::DATETIME_SEPARATOR . '.+)?$/',
            $representation,
            $matches
        )
        ) {

            $date = null;
            if (isset($matches[1])) {
                $date = ($matches[1] === '' ? null : $matches[1]);
            }

            $time = array_key_exists(2, $matches) ? substr($matches[2], 1) : null;

            if ($date !== null && !preg_match(
                    '/^(\d+[' . self::YEARS_CODE_DUTCH . '|' . self::YEARS_CODE_ENGLISH . '])?(\d+' . self::MONTHS_CODE . ')?(\d+' . self::DAYS_CODE . ')?$/',
                    $date
                )
            ) {
                return null;
            }
            if ($time !== null && !preg_match(
                    '/^(\d+' . self::HOURS_CODE . ')?(\d+' . self::MINUTES_CODE . ')?(\d+(\.\d+)?' . self::SECONDS_CODE . ')?$/',
                    $time
                )
            ) {
                return null;
            }

            if (preg_match(
                '/(\d+)[' . self::YEARS_CODE_ENGLISH . '|' . self::YEARS_CODE_DUTCH . ']/',
                $date,
                $matches
            )
            ) {
                $duration->hours += $matches[1] * self::DAYS_PER_YEAR * self::HOURS_PER_DAY;
            }
            if (preg_match('/(\d+)' . self::MONTHS_CODE . '/', $date, $matches)) {
                $duration->hours += $matches[1] * self::DAYS_PER_MONTH * self::HOURS_PER_DAY;
            }
            if (preg_match('/(\d+)' . self::DAYS_CODE . '/', $date, $matches)) {
                $duration->hours += $matches[1] * self::HOURS_PER_DAY;
            }

            if (preg_match('/(\d+)' . self::HOURS_CODE . '/', $time, $matches)) {
                $duration->hours += (int)$matches[1];
            }
            if (preg_match('/(\d+)' . self::MINUTES_CODE . '/', $time, $matches)) {
                $duration->minutes = (int)$matches[1];
            }
            if (preg_match('/(\d+(\.\d+)?)' . self::SECONDS_CODE . '/', $time, $matches)) {
                $duration->seconds = (int)$matches[1];
            }

            $duration->normalize();
            if ($duration->isValid()) {
                return $duration;
            }
        }

        return null;
    }

    private function isValid()
    {
        return $this->hours !== null || $this->minutes !== null || $this->seconds !== null;
    }

    private function normalize()
    {
        $this->hours = $this->normalizeValue($this->hours);
        $this->minutes = $this->normalizeValue($this->minutes);
        $this->seconds = $this->normalizeValue($this->seconds);
    }

    private function normalizeValue($value)
    {
        if ($value !== null) {
            $value = preg_replace('/^0+$/', '', $value);
            $value = preg_replace('/^(0+)(\d)/', '$2', $value);
            $value = preg_replace('/^0+\.0+/', '', $value);
            if ($value !== '') {
                return $value;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getHours()
    {
        return $this->hours;
    }

    public function getMinutes()
    {
        return $this->minutes;
    }

    public function getSeconds()
    {
        return $this->seconds;
    }

    public function setHours($hours)
    {
        $this->hours = $hours;
    }

    public function setMinutes($minutes)
    {
        $this->minutes = $minutes;
    }

    public function setSeconds($seconds)
    {
        $this->seconds = $seconds;
    }

    public function __toString()
    {
        if ($this->isValid()) {
            $time = ($this->hours === null ? '' : $this->hours . self::HOURS_CODE)
                . ($this->minutes === null ? '' : $this->minutes . self::MINUTES_CODE)
                . ($this->seconds === null ? '' : $this->seconds . self::SECONDS_CODE);

            return 'P' . ($time === '' ? '' : self::DATETIME_SEPARATOR . $time);
        }
    }
}