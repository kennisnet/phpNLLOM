<?php

namespace Kennisnet\NLLOM\Library;
/**
 * lom.lifecycle.contribute item
 */
class LomRelation
{
    private $kind;
    private $resources = [];
    private $description;

    /**
     * LomRelation constructor.
     * @param LomTerm|null $kind
     * @param LomIdentifier[] $resources
     * @param LomMultiLanguage|null $description
     */
    public function __construct(
        LomTerm $kind = null,
        array $resources,
        LomMultiLanguage $description = null
    ) {
        $this->kind = $kind;
        $this->resources = $resources;
        $this->description = $description;
    }

    /**
     * @return LomTerm
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * @return LomIdentifier[]
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * @return LomMultiLanguage|null
     */
    public function getDescription()
    {
        return $this->description;
    }
}
