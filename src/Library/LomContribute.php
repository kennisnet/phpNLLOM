<?php

namespace Kennisnet\NLLOM\Library;
/**
 * lom.lifecycle.contribute item
 */
class LomContribute
{
    private $role;
    private $entities = [];
    private $dateTime;

    /**
     * LomContribute constructor.
     * @param LomTerm $role
     * @param LomString[]|array $entities
     * @param LomDateTime|null $dateTime
     */
    public function __construct(
        LomTerm $role,
        array $entities = [],
        LomDateTime $dateTime = null
    ) {
        $this->role = $role;
        $this->entities = $entities;
        $this->dateTime = $dateTime;
    }

    /**
     * gets the role
     * @return LomTerm role term or NULL if not set
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return array|LomString[]
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * @return LomDateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

}
