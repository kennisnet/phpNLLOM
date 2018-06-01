<?php

namespace Kennisnet\NLLOM;

use Kennisnet\NLLOM\Library\LomContribute;

class NLLOM extends Lom
{
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
