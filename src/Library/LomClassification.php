<?php

namespace Kennisnet\NLLOM\Library;
/**
 * Taxon entry
 */
class LomClassification
{
    /**
     * @var LomTerm
     */
    private $purpose;

    /**
     * @var LomTaxonPath[]
     */
    private $taxonPaths = [];

    /**
     * LomClassification constructor.
     * @param LomTerm $purpose
     * @param LomTaxonPath[]
     */
    public function __construct(
        LomTerm $purpose,
        array $lomTaxonPaths
    ) {
        $this->purpose = $purpose;
        $this->taxonPaths = $lomTaxonPaths;
    }

    /**
     * @return LomTerm
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * @return LomTaxonPath[]
     */
    public function getTaxonPaths()
    {
        return $this->taxonPaths;
    }
}
