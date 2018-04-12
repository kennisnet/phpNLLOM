<?php

namespace Kennisnet\NLLOM\Library;
/**
 * Taxon entry
 */
class LomTaxon
{
    /**
     * @var LomString id
     */
    private $id;

    /**
     * @var LomMultiLanguage
     */
    private $taxonEntry;

    public function __construct(
        LomString $id,
        LomMultiLanguage $taxonEntry
    ) {
        $this->id = $id;
        $this->taxonEntry = $taxonEntry;
    }

    /**
     * @return LomString id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return LomMultiLanguage
     */
    public function getTaxonEntry()
    {
        return $this->taxonEntry;
    }


}
