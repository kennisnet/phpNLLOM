<?php

namespace Kennisnet\NLLOM\Library;

class LomMultiLanguage
{
    private $languageStrings = [];

    /**
     * LomMultiLanguage constructor.
     * @param LomLanguageString[] $languageStrings
     * @throws \InvalidArgumentException
     */
    public function __construct(array $languageStrings)
    {
        if (count($languageStrings) === 0) {
            throw new \InvalidArgumentException('Need at least one LomLanguageString');
        }

        $this->languageStrings = $languageStrings;
    }

    /**
     * @return LomLanguageString[]
     */
    public function getLanguageStrings()
    {
        return $this->languageStrings;
    }
}
