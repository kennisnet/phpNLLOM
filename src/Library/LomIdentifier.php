<?php
namespace Kennisnet\NLLOM\Library;
/**
 * LomCatalogEntry
 */
class LomIdentifier
{
    private $catalog;
    private $entry;

    public function __construct($catalog, $entry)
    {
        $this->catalog = $catalog;
        $this->entry = $entry;
    }

    public function getCatalog()
    {
        return $this->catalog;
    }

    public function getEntry()
    {
        return $this->entry;
    }
}
