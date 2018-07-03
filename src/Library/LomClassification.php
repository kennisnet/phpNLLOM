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
     * @var LomString[]
     */
    private $keywords = [];

    /**
     * LomClassification constructor.
     * @param LomTerm $purpose
     * @param LomTaxonPath[]
     * @param LomString[]
     */
    public function __construct(
        LomTerm $purpose,
        array $lomTaxonPaths,
        array $keywords=[]
    ) {
        $this->purpose = $purpose;
        $this->taxonPaths = $lomTaxonPaths;
        $this->keywords = $keywords;
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

    /**
     * @return LomString[]
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
}
