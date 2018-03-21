<?php

namespace Kennisnet\NLLOM;

use Kennisnet\NLLOM\Library\LomClassification;
use Kennisnet\NLLOM\Library\LomContribute;
use Kennisnet\NLLOM\Library\LomDateTime;
use Kennisnet\NLLOM\Library\LomDuration;
use Kennisnet\NLLOM\Library\LomIdentifier;
use Kennisnet\NLLOM\Library\LomInterval;
use Kennisnet\NLLOM\Library\LomLanguageString;
use Kennisnet\NLLOM\Library\LomMultiLanguage;
use Kennisnet\NLLOM\Library\LomRelation;
use Kennisnet\NLLOM\Library\LomString;
use Kennisnet\NLLOM\Library\LomTaxon;
use Kennisnet\NLLOM\Library\LomTaxonPath;
use Kennisnet\NLLOM\Library\LomTerm;

class DomToLomMapper
{
    const NS_LOM = 'http://www.imsglobal.org/xsd/imsmd_v1p2';
    const NS_XML_NAMESPACE = 'http://www.w3.org/XML/1998/namespace';

    const LOM_VERSION = 'LOMv1.0';
    const INTENDEDENDUSERROLE_DEFAULT = 'learner';

    /**
     * @var \DOMXPath
     */
    private $xp;

    /**
     * Parse DOM to object
     *
     * Use relfection class to find all setters for NLLOM and
     * call matching getters on our parser class.
     *
     * @param \DOMDocument $doc
     * @param array $options Optional options that gets passed to newly created NLLOM
     * @return NLLOM
     */
    public function domToLom(\DOMDocument $doc, array $options = [])
    {
        $this->xp = new \DOMXPath($doc);
        $this->xp->registerNamespace('lom', static::NS_LOM);

        $nllom = new NLLOM($options);

        //Use some reflection magic to quickly set all properties on the Lom object:
        //
        // - find all set/add methods
        // - match them with get methods in this class
        // - call set/add on NLLOM with get in this class
        try {
            $class = new \ReflectionClass(Lom::class);
        } catch (\ReflectionException $e) {

        }

        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            $setter = $method->getName();

            if (strpos($setter, 'set') === 0) {
                $getter =  str_replace('set', 'get', $setter);
                $result = $this->$getter();

                if ($result) {
                    $nllom->$setter($result);
                }
            } elseif (strpos($setter, 'add') === 0) {
                $getter =  str_replace('add', 'get', $setter);

                //addX methods are used for multivalue properties,
                //so make the getter in the syle of 'getValue[s]'
                $getter .= 's';

                $results = $this->$getter();

                foreach ($results as $result) {
                    $nllom->$setter($result);
                }
            }
        }

        return $nllom;
    }

    /**
     * @return LomMultiLanguage|null
     */
    public function getGeneralTitle()
    {
        $query = '/lom:lom/lom:general/lom:title/lom:langstring';

        return $this->getMultiLanguage($query);
    }

    /**
     * @param $languageCode
     * @return array
     */
    public function getGeneralDescriptions()
    {
        $language = [];

        foreach ($this->query('/lom:lom/lom:general/lom:description') as $node) {
            $language[] = $this->getMultiLanguage('lom:langstring', [], $node);
        }

        return $language;
    }

    /**
     * @param $languageCode
     * @return LomLanguageString[]
     */
    public function getGeneralKeywords()
    {
        $language = [];

        foreach ($this->query('/lom:lom/lom:general/lom:keyword') as $node) {
            $language[] = $this->getMultiLanguage('lom:langstring', [], $node);
        }

        return $language;
    }

    public function getGeneralCoverages()
    {
        $language = [];

        foreach ($this->query('/lom:lom/lom:general/lom:coverage') as $node) {
            $language[] = $this->getMultiLanguage('lom:langstring', [], $node);
        }

        return $language;
    }

    /**
     * @return LomString[]
     */
    public function getGeneralLanguages()
    {
        $query = '/lom:lom/lom:general/lom:language';

        return $this->mapNodeListToLomStrings($this->query($query));
    }

    public function getGeneralStructure()
    {
        $query = '/lom:lom/lom:general/lom:structure';

        $lomTerm = $this->getTerm($query);
        return $lomTerm;
    }

    public function getEducationalLanguages()
    {
        $query = '/lom:lom/lom:educational/lom:language';

        return $this->mapNodeListToLomStrings($this->query($query));
    }

    public function getEducationalDescriptions()
    {
        $language = [];

        foreach ($this->query('/lom:lom/lom:educational/lom:description') as $node) {
            $language[] = $this->getMultiLanguage('lom:langstring', [], $node);
        }

        return $language;
    }

    public function getEducationalIntendedUserRoles()
    {
        $query = '/lom:lom/lom:educational/lom:intendedenduserrole';
        $values = [];
        foreach ($this->query($query) as $node) {
            $lomTerm = $this->mapNodeToLomTerm($node);
            if ($lomTerm !== null) {
                $values[] = $lomTerm;
            }
        }

        // NL LOM (9 jun 2011) states that if no intended end user role is specified
        // the system should interpret it as if "learner" was present
        if (empty($values)) {
            //TODO disabled this to keep everything optional
            //$values[] = new LomTerm(static::INTENDEDENDUSERROLE_DEFAULT, static::LOM_VERSION);
        }

        return $values;
    }

    public function getEducationalInteractivityLevel()
    {
        $query = '/lom:lom/lom:educational/lom:interactivitylevel';

        $lomTerm = $this->getTerm($query);
        return $lomTerm;
    }

    public function getEducationalSemanticDensity()
    {
        $query = '/lom:lom/lom:educational/lom:semanticdensity';

        $lomTerm = $this->getTerm($query);
        return $lomTerm;
    }

    public function getEducationalContexts()
    {
        $query = '/lom:lom/lom:educational/lom:context';
        $values = [];
        foreach ($this->query($query) as $node) {
            $lomTerm = $this->mapNodeToLomTerm($node);
            if ($lomTerm !== null) {
                $values[] = $lomTerm;
            }
        }

        return $values;
    }

    public function getEducationalDifficulty()
    {
        $query = '/lom:lom/lom:educational/lom:difficulty';

        $lomTerm = $this->getTerm($query);
        return $lomTerm;
    }

    public function getGeneralAggregationLevel()
    {
        $query = '(/lom:lom/lom:general/lom:aggregationlevel)[1]';
        foreach ($this->query($query) as $node) {
            return $this->mapNodeToLomTerm($node);
        }

        return null;
    }

    public function getEducationalLearningResourceTypes()
    {
        $query = '/lom:lom/lom:educational/lom:learningresourcetype';
        $values = [];
        foreach ($this->query($query) as $node) {
            $values[] = $this->mapNodeToLomTerm($node);
        }

        return $values;
    }

    public function getGeneralIdentifiers()
    {
        $query = '/lom:lom/lom:general/lom:catalogentry[lom:catalog[normalize-space(.) != \'\'] and
            lom:entry/lom:langstring[normalize-space(.) != \'\']]';

        return $this->mapIdentifiers($query);
    }

    /**
     * @return LomMultiLanguage|null
     */
    public function getTechnicalRemarks()
    {
        $query = '/lom:lom/lom:technical/lom:installationremarks/lom:langstring';

        return $this->getMultiLanguage($query);
    }

    public function getTechnicalDuration($lang = 'nl')
    {
        $query = '/lom:lom/lom:technical/lom:duration';

        return $this->mapDuration($query, $lang);
    }

    public function getTechnicalFormats()
    {
        $query = '/lom:lom/lom:technical/lom:format';
        $results = [];
        foreach ($this->query($query) as $node) {
            $results[] = new LomString($node->textContent);
        }

        return $results;
    }

    public function getTechnicalLocations()
    {
        $query = '/lom:lom/lom:technical/lom:location';
        $results = [];
        foreach ($this->query($query) as $node) {
            $results[] = new LomString($node->textContent);
        }

        return $results;
    }

    public function getTechnicalSize()
    {
        $query = '/lom:lom/lom:technical/lom:size';

        $lomTerm = $this->getString($query);
        return $lomTerm;
    }

    public function getEducationalInteractivityType()
    {
        $lomTerm = $this->getTerm('/lom:lom/lom:educational/lom:interactivitytype[1]');
        return $lomTerm;
    }

    public function getEducationalTypicalAgeRanges()
    {
        $query = '/lom:lom/lom:educational/lom:typicalagerange/lom:langstring';
        $results = [];
        foreach ($this->query($query) as $node) {
            $results[] = new LomString($node->textContent);
        }

        return $results;
    }

    public function getEducationalTypicalLearningTime()
    {
        $query = '/lom:lom/lom:educational/lom:typicallearningtime';

        return $this->mapDuration($query);
    }

    public function getRightsCost()
    {
        $query = '/lom:lom/lom:rights/lom:cost[1]';

        return $this->getTerm($query);
    }

    public function getRightsCopyright()
    {
        $lomTerm = $this->getTerm('/lom:lom/lom:rights/lom:copyrightandotherrestrictions[1]');

        return $lomTerm;
    }

    /**
     * @return LomMultiLanguage|null
     */
    public function getRightsDescription()
    {
        $query = '/lom:lom/lom:rights/lom:description/lom:langstring';

        return $this->getMultiLanguage($query);
    }

    /**
     * @return LomClassification[]
     */
    public function getClassifications()
    {
        $classifications = [];

        foreach ($this->query('/lom:lom/lom:classification') as $classification) {
            $purpose = $this->getTerm('lom:purpose', [], $classification);

            if (!$purpose) {
                continue;
            }

            $taxonpaths = [];

            foreach ($this->query('lom:taxonpath', [], $classification) as $taxonpath) {
                $sourceValue = $this->xp->evaluate('normalize-space(lom:source/lom:langstring)', $taxonpath);
                $source = new LomString($sourceValue);

                $taxons = [];

                foreach ($this->query('lom:taxon', [], $taxonpath) as $taxon) {
                    $id = new LomString($this->xp->evaluate('normalize-space(lom:id)', $taxon));

                    $entry = $this->getMultiLanguage('(lom:entry/lom:langstring)', [], $taxon);

                    if (!$entry) {
                        continue;
                    }

                    $taxons[] = new LomTaxon($id, $entry);
                }

                $taxonpaths[] = new LomTaxonPath(
                    $source,
                    $taxons
                );
            }

            $classifications[] = new LomClassification(
                $purpose,
                $taxonpaths
            );

        }

        return $classifications;
    }

    public function getLifecycleStatus()
    {
        $lomTerm = $this->getTerm('/lom:lom/lom:lifecycle/lom:status[1]');
        return $lomTerm;
    }

    public function getLifecycleVersion()
    {
        $query = '/lom:lom/lom:lifecycle/lom:version/lom:langstring';

        $lomTerm = $this->getString($query);
        return $lomTerm;
    }

    public function getLifecycleContributors()
    {
        $query = '/lom:lom/lom:lifecycle/lom:contribute';
        return $this->mapContributor($query);
    }

    public function getMetaMetadataContributors()
    {
        $query = '/lom:lom/lom:metametadata/lom:contribute';
        return $this->mapContributor($query);
    }

    public function getMetaMetadataLanguage()
    {
        $query = '/lom:lom/lom:metametadata/lom:language';

        $lomTerm = $this->getString($query);
        return $lomTerm;
    }

    public function getMetaMetadataSchemas()
    {
        $query = '/lom:lom/lom:metametadata/lom:metadatascheme';

        $results = [];
        foreach ($this->query($query) as $node) {
            $results[] = new LomString($node->textContent);
        }

        return $results;
    }

    public function getMetaMetadataIdentifiers()
    {
        $query = '/lom:lom/lom:metametadata/lom:catalogentry[lom:catalog[normalize-space(.) != \'\'] and 
            lom:entry/lom:langstring[normalize-space(.) != \'\']]';

        return $this->mapIdentifiers($query);
    }

    public function getRelations()
    {
        $items = [];

        foreach ($this->query('/lom:lom/lom:relation') as $relation) {
            $kind = $this->getTerm('lom:kind', [], $relation);

            $description = $this->getMultiLanguage(
                '(lom:resource/lom:description/lom:langstring)',
                [],
                $relation
            );

            $query = 'lom:resource/lom:catalogentry[lom:catalog[normalize-space(.) != \'\'] and 
                lom:entry/lom:langstring[normalize-space(.) != \'\']]';
            $catalogEntries = $this->mapIdentifiers($query, $relation);

            $items[] = new LomRelation(
                $kind,
                $catalogEntries,
                $description
            );
        }

        return $items;
    }

    private function mapNodeListToLomStrings(\DOMNodeList $nodeList)
    {
        $results = [];
        foreach ($nodeList as $node) {
            $results[] = new LomString($node->textContent);
        }

        return $results;
    }

    /**
     * removes nodes from the document
     *
     * @param \DOMNodeList $nodeList
     */
    private function removeNodes(\DOMNodeList $nodeList)
    {
        foreach ($nodeList as $node) {
            $this->removeNode($node);
        }
    }

    private function removeNode(\DOMNode $node)
    {
        $node->parentNode->removeChild($node);
    }

    /**
     * @param $xpath
     * @param array $params
     * @param \DOMNode|null $contextNode
     * @return LomMultiLanguage|null
     */
    private function getMultiLanguage($xpath, array $params = [], \DOMNode $contextNode = null)
    {
        $results = [];
        foreach ($this->query($xpath, $params, $contextNode) as $langstring) {
            $results[] = $this->mapLanguageString($langstring);
        }

        return $results ? new LomMultiLanguage($results): null;
    }

    private function getTerm($xpath, array $params = [], \DOMNode $contextNode = null)
    {
        foreach ($this->query($xpath, $params, $contextNode) as $node) {
            return $this->mapNodeToLomTerm($node);
        }

        return null;
    }

    private function getString($xpath, array $params = [], \DOMNode $contextNode = null)
    {
        foreach ($this->query($xpath, $params, $contextNode) as $node) {
            return new LomString($node->textContent);
        };

        return null;
    }

    /**
     * @param $xpath
     * @return array
     */
    private function mapContributor($xpath)
    {
        $items = [];

        foreach ($this->query($xpath) as $contribute) {
            $role = $this->getTerm('lom:role', [], $contribute);

            if (!$role) {
                continue;
            }

            $dateTime = $this->getString('(lom:date/lom:datetime)[1]', [], $contribute);
            $dateTimeDescriptions = $this->getMultiLanguage(
                '(lom:date/lom:description/lom:langstring[@xml:lang=$lang])[1]',
                ['$lang' => 'nl'],
                $contribute
            );
            if (empty($dateTimeDescriptions)) {
                $dateTimeDescriptions = $this->getMultiLanguage(
                    '(lom:date/lom:description/lom:langstring)[1]',
                    [],
                    $contribute
                );
            }
            $dateTimeDescription = empty($dateTimeDescriptions) ? null : $dateTimeDescriptions;

            $entities = [];

            foreach ($this->query('lom:centity/lom:vcard', [], $contribute) as $vcard) {
                $entities[] = new LomString($vcard->textContent);
            }

            $items[] = new LomContribute(
                $role,
                $entities,
                $dateTime ? new LomDateTime(
                    $dateTime,
                    $dateTimeDescription
                ) : null
            );
        }

        return $items;
    }


    private function mapLanguageString(\DOMElement $element)
    {
        $languageCode = (string)$this->xp->evaluate('string(@xml:lang)', $element);
        $value = $element->textContent;

        return new LomLanguageString($value, $languageCode);
    }

    /**
     *
     * @param \DOMElement $node
     * @return LomTerm lom term
     */
    private function mapNodeToLomTerm(\DOMElement $node)
    {
        $source = null;
        $value = null;
        foreach ($this->query('lom:source/lom:langstring[@xml:lang="x-none"][1]', [], $node) as $sourceNode) {
            $source = $sourceNode->textContent;
        }
        foreach ($this->query('lom:value/lom:langstring[@xml:lang="x-none"][1]', [], $node) as $valueNode) {
            $value = $valueNode->textContent;
        }

        if ($source === null || $value === null) {
            return null;
        } else {
            return new LomTerm($value, $source);
        }
    }

    /**
     * @param $xpath
     * @param \DOMElement|null $parent
     * @return LomIdentifier[]
     */
    private function mapIdentifiers($xpath, \DOMElement $parent = null)
    {
        $catalogEntries = [];

        foreach ($this->query($xpath, [], $parent) as $node) {
            $catalog = (string)$this->xp->evaluate('normalize-space(lom:catalog)', $node);
            $entry = (string)$this->xp->evaluate('normalize-space(lom:entry/lom:langstring)', $node);

            $catalogEntries[] = new LomIdentifier($catalog, $entry);
        }

        return $catalogEntries;
    }

    /**
     * @param $xpath
     * @param array $params
     * @param null $contextNode
     * @return \DOMNodeList
     */
    private function query($xpath, array $params = [], $contextNode = null)
    {
        $query = $this->escapeXpath($xpath, $params);

        if ($contextNode === null) {
            return $this->xp->query($query);
        } else {
            return $this->xp->query($query, $contextNode);
        }
    }

    private function escapeXpath($query, array $params = [])
    {
        $replacements = [];
        foreach ($params as $key => $value) {
            if (is_string($value)) {
                $value = $this->escapeXpathValue($value);
            }
            $replacements[$key] = $value;
        }
        $query = strtr($query, $replacements);

        return $query;
    }

    private function escapeXpathValue($value)
    {
        $escaped = '';
        if (strpos($value, '\'') === false) {
            $escaped = '\'' . $value . '\'';
        } else {
            $escaped = 'concat(';
            for ($i = 0; $i < strlen($value); ++$i) {
                if ($i > 0) {
                    $escaped .= ',';
                }
                $char = substr($value, $i, 1);
                if ($char === '\'') {
                    $escaped .= '"\'"';
                } else {
                    $escaped .= '\'' . $char . '\'';
                }
            }
            $escaped .= ')';
        }

        return $escaped;
    }


    private function assertValid()
    {
        $this->assertEducationalHasRequiredElements();
        $this->assertEmptyElementsAreRemoved();
    }

    private function mapDuration($xpath, $lang = 'nl')
    {
        $duration = $this->query($xpath) [0];

        if (!$duration) {
            return null;
        }

        $dateTime = $this->getString('lom:datetime', [], $duration);

        $dateTimeDescriptions = $this->getMultiLanguage(
            '(lom:description/lom:langstring)',[],
            $duration
        );

        return new LomDuration(
            new LomInterval($dateTime),
            $dateTimeDescriptions
        );
    }

    private function assertEducationalHasRequiredElements()
    {
        // copy intendedenduserrole
        foreach ($this->query(
            '/lom:lom/lom:educational[not(lom:intendedenduserrole)]'
        ) as $educationalWithoutIntendedEndUserRole) {
            foreach ($this->query(
                '(/lom:lom/lom:educational[lom:intendedenduserrole])[1]/lom:intendedenduserrole'
            ) as $intendedenduserrole) {
                $clone = $intendedenduserrole->cloneNode(true);

                $xpath = '(lom:context | lom:typicalagerange | lom:difficulty | 
                    lom:typicallearningtime | lom:description | lom:language)[1]';

                $nl = $this->query(
                    $xpath,
                    [],
                    $educationalWithoutIntendedEndUserRole
                );

                $nextSibling = ($nl->length === 0 ? null : $nl->item(0));
                $this->insertBefore($educationalWithoutIntendedEndUserRole, $clone, $nextSibling);
            }
        }

        // copy typicalagerange
        foreach ($this->query(
            '/lom:lom/lom:educational[not(lom:typicalagerange)]'
        ) as $educationalWithoutTypicalAgeRange) {
            foreach ($this->query(
                '(/lom:lom/lom:educational[lom:typicalagerange])[1]/lom:typicalagerange'
            ) as $typicalagerange) {
                $clone = $typicalagerange->cloneNode(true);
                $nl = $this->query(
                    '(lom:difficulty | lom:typicallearningtime | lom:description | lom:language)[1]',
                    [],
                    $educationalWithoutTypicalAgeRange
                );
                $nextSibling = ($nl->length === 0 ? null : $nl->item(0));
                $this->insertBefore($educationalWithoutTypicalAgeRange, $clone, $nextSibling);
            }
        }
    }

    private function assertEmptyElementsAreRemoved()
    {
        // remove empty descriptions
        $this->removeNodes($this->query('/lom:lom/lom:general/lom:description[normalize-space(.) =\'\']'));

        // remove empty lifecycle nodes
        $this->removeNodes($this->query('/lom:lom/lom:lifecycle[not(*)]'));
    }

}
