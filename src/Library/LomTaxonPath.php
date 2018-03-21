<?php

namespace Kennisnet\NLLOM\Library;

class LomTaxonPath
{
    /**
     * @var LomString[]
     */
    private $source;

    /**
     * @var LomTaxon[]
     */
    private $taxons = [];

    /**
     * LomTaxonPath constructor.
     * @param LomString $source
     * @param LomTaxon[] $taxons
     */
    public function __construct(
        LomString $source,
        array $taxons
    ) {
        $this->source = $source;
        $this->taxons = $taxons;
    }

    /**
     * @return LomString[]
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return LomTaxon[]
     */
    public function getTaxons()
    {
        return $this->taxons;
    }

}
