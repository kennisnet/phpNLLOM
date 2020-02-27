<?php

namespace Kennisnet\NLLOM;

use Kennisnet\NLLOM\Library\LomContribute;

class NLLOM extends Lom
{
    /**
     * @return string
     */
    public function getTitle()
    {
        if($this->getGeneralTitle()) {
            return $this->getGeneralTitle()->getLanguageStrings()[0]->getValue();
        }
        return '';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $descriptions = $this->getGeneralDescriptions();
        if (isset($descriptions[0])) {
            return $descriptions[0]->getLanguageStrings()[0]->getValue();
        }
    }

    /**
     * @return array
     */
    public function getCatalogEntryUris()
    {
        $results = [];
        foreach ($this->getGeneralIdentifiers() as $resourceType) {
            $results[] = $resourceType->getEntry();
        }
        return $results;
    }

    /**
     * @return array
     */
    public function getLearningResourceTypes()
    {
        $results = [];
        foreach ($this->getEducationalLearningResourceTypes() as $resourceType) {
            $results[] = $resourceType->getValue();
        }
        return $results;
    }

    /**
     * @return array
     */
    public function getPublishers()
    {
        return self::getEntities($this->getLifecycleContributors(), 'publisher');
    }

    /**
     * @return \DateTime
     */
    public function getPublishDate()
    {
        foreach ($this->getLifecycleContributors() as $contributor) {
            if ($contributor->getRole()->getValue() === 'publisher') {
                if ($contributor->getDateTime()) {
                    return (new \DateTime((string)$contributor->getDateTime()->getDateTime()));
                }
            }
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getTechnicalLocation()
    {
        if (isset($this->getTechnicalLocations()[0])) {
            return $this->getTechnicalLocations()[0]->getValue();
        }
    }

    /**
     * @return array
     */
    public function getAuthors()
    {
        return self::getEntities($this->getLifecycleContributors(), 'author');
    }

    /**
     * @return array
     */
    public function getCreators()
    {
        return self::getEntities($this->getMetaMetadataContributors(), 'creator');
    }

    /**
     * @return array
     */
    public function getContentProviders()
    {
        return self::getEntities($this->getLifecycleContributors(), 'content provider');
    }

    /**
     * @return string|null
     */
    public function getThumbnail()
    {
        foreach ($this->getRelations() as $relation) {
            if ($relation->getKind()) {
                switch ($relation->getKind()->getValue()) {
                    case 'thumbnail':
                        return $relation->getResources()[0]->getEntry();
                }
            }
        }

        return null;
    }

    /**
     * @param $entity
     * @return string
     */
    public static function parseEntity($entity)
    {
        $matches = [];

        // Check for both FN: and N: format, and fetch the first match
        preg_match_all('/^(F?N|ORG):(.*?)$/m', $entity, $matches);

        return isset($matches[2][0]) ? $matches[2][0] : '';
    }

    /**
     * @param LomContribute[] $contributors
     * @param string $type
     * @return array
     */
    private static function getEntities($contributors, $type)
    {
        $entities = [];

        foreach ($contributors as $contributor) {
            if ($contributor->getRole()->getValue() === $type) {
                if (isset($contributor->getEntities()[0])) {
                    $entities[] = (self::parseEntity($contributor->getEntities()[0]->getValue()));
                }
            }
        }

        return $entities;
    }
}
