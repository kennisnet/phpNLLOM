<?php
namespace Kennisnet\NLLOM;

use Kennisnet\NLLOM\Library\LomContribute;
use Kennisnet\NLLOM\Library\LomDateTime;
use Kennisnet\NLLOM\Library\LomDuration;
use Kennisnet\NLLOM\Library\LomIdentifier;
use Kennisnet\NLLOM\Library\LomLanguageString;
use Kennisnet\NLLOM\Library\LomMultiLanguage;
use Kennisnet\NLLOM\Library\LomTerm;

class LomToDomMapper
{
    const XMLNS = "http://www.imsglobal.org/xsd/imsmd_v1p2";
    const XMLNS_XSI = "http://www.w3.org/2001/XMLSchema-instance";
    const XSI_SCHEMALOCATION = "http://www.imsglobal.org/xsd/imsmd_v1p2 http://www.imsglobal.org/xsd/imsmd_v1p2p4.xsd";
    const RELATION_VOCAB = 'http://purl.edustandaard.nl/relation_kind_nllom_20131211';
    const LOM_VERSION = 'LOMv1.0';
    const LOM_SCHEMA = 'nl_lom_v1p0';

    private $dom;
    private $options;

    /**
     * @var Lom
     */
    private $nllom;

    /**
     * LomToDomMapper constructor.
     */
    public function __construct()
    {
        $domDocument = new \DOMDocument('1.0', 'UTF-8');

        $this->dom = $domDocument;
        $domDocument->formatOutput = true;
        $domDocument->preserveWhiteSpace = false;
    }

    /**
     * @param Lom $nllom
     * @return \DOMDocument
     */
    public function lomToDom(Lom $nllom)
    {
        $this->nllom = $nllom;

        $root = $this->dom->createElementNS(self::XMLNS, 'lom');
        $this->dom->appendChild($root);

        $root->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance'
        );
        $root->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation', self::XSI_SCHEMALOCATION);

        $general = $this->dom->createElement('general');
        $this->domSetTitle($general);
        $this->domAddIdentifiers($general);
        $this->domLanguages($general);
        $this->domSetDescription($general);
        $this->domAddKeywords($general);
        $this->domAddCoverage($general);
        $this->domSetStructure($general);

        $this->domSetAggregationLevel($general);

        if ($general->hasChildNodes()) {
            $root->appendChild($general);
        }

        $this->domSetLifecycle($root);

        $metametadata = $this->dom->createElement('metametadata');
        $this->domAddMetaMetadataIdentifiers($metametadata);
        $this->domSetMetametadata($metametadata);

        if ($metametadata->hasChildNodes()) {
            $root->appendChild($metametadata);
        }

        $technical = $this->dom->createElement('technical');
        $this->domSetTechnical($technical);
        if ($technical->hasChildNodes()) {
            $root->appendChild($technical);
        }

        $educational = $this->dom->createElement('educational');
        $this->domSetEducational($educational);

        if ($educational->hasChildNodes()) {
            $root->appendChild($educational);
        }

        $rights = $this->dom->createElement('rights');
        $this->domSetRights($rights);

        if ($rights->hasChildNodes()) {
            $root->appendChild($rights);
        }

        $this->domAddRelations($root);

        $this->domAddClassifications($root);

        return $this->dom;
    }

    private function domSetTitle(\DOMElement $general)
    {
        if ($this->nllom->getGeneralTitle()) {
            $this->parseLomLanguageString($this->nllom->getGeneralTitle(), 'title', $general);
        }
    }

    private function domSetDescription(\DOMElement $general)
    {
        foreach ($this->nllom->getGeneralDescriptions() as $generalDescription) {
            $this->parseLomLanguageString($generalDescription, 'description', $general);
        }
    }

    private function domAddIdentifiers(\DOMElement $general)
    {
        foreach ($this->nllom->getGeneralIdentifiers() as $lomCatalogEntry) {
            $this->parseLomIdentifier($lomCatalogEntry, $general);
        }
    }

    private function domAddMetaMetadataIdentifiers(\DOMElement $el)
    {
        foreach ($this->nllom->getMetaMetadataIdentifiers() as $lomCatalogEntry) {
            $this->parseLomIdentifier($lomCatalogEntry, $el);
        }
    }


    private function domAddCoverage(\DOMElement $general)
    {
        foreach ($this->nllom->getGeneralCoverages() as $coverage) {
            $this->parseLomLanguageString($coverage, 'coverage', $general);
        }
    }

    private function domSetStructure(\DOMElement $general)
    {
        if ($this->nllom->getGeneralStructure()) {
            $this->parseLomTerm($this->nllom->getGeneralStructure(), 'structure', $general);
        }
    }

    /**
     * Add keywords to DomElement
     *
     * @param \DOMElement $element
     */
    private function domAddKeywords(\DOMElement $element)
    {
        foreach ($this->nllom->getGeneralKeywords() as $lomLanguageString) {
            $this->parseLomLanguageString($lomLanguageString, 'keyword', $element);
        }
    }


    /**
     * @param \DOMElement $element
     */
    private function domLanguages(\DOMElement $element)
    {
        foreach ($this->nllom->getGeneralLanguages() as $language) {
            $node = $this->dom->createElement('language', $language);
            $element->appendChild($node);
        }
    }

    //TODO check required
    /**
     * @param \DOMElement $element
     *
     * Conditional Fields
     * Rule
     * Page
     * 5.6 Context    Gebruik is OPTIONEEL wanneer in veld 5.6 een ander vocabulaire dan http://purl.edustandaard.nl/vdex_context_czp_20060628.xml wordt gebruikt.    Aggregatieniveau andere contexten
     * 5.6 Context    Gebruik is AANBEVOLEN wanneer in veld 5.6 het vocabulaire http://purl.edustandaard.nl/vdex_context_czp_20060628.xml wordt gebruikt met een van de volgende waarden HBO|WO|bedrijfsopleiding.    Aggregatieniveau HO
     * 5.6 Context    Gebruik is VERPLICHT wanneer in veld 5.6 het vocabulaire http://purl.edustandaard.nl/vdex_context_czp_20060628.xml wordt gebruikt met een van de volgende waarden PO|VO|BVE|SO|SBaO|VVE.
     */
    private function domSetAggregationLevel(\DOMElement $element)
    {
        $generalAggregationLevel = $this->nllom->getGeneralAggregationLevel();

        if ($generalAggregationLevel) {
            $this->parseLomTerm($generalAggregationLevel, 'aggregationlevel', $element);
        }
    }

    private function domSetLifecycle(\DomNode $root)
    {
        $lifecycle = $this->dom->createElement('lifecycle');

        if ($this->nllom->getLifecycleVersion()) {
            $node = $this->dom->createElement('version');
            $node->appendChild($this->createLangstring($this->nllom->getLifecycleVersion()->getValue()));
            $lifecycle->appendChild($node);
        }

        if ($this->nllom->getLifecycleStatus()) {
            $this->parseLomTerm($this->nllom->getLifecycleStatus(), 'status', $lifecycle);
        }

        foreach ($this->nllom->getLifecycleContributors() as $lifecycleContributor) {
            $this->parseLomContributor($lifecycleContributor, $lifecycle);
        }

        if ($lifecycle->hasChildNodes()) {
            $root->appendChild($lifecycle);
        }
    }

    private function domSetMetametadata(\DomNode $root)
    {
        foreach ($this->nllom->getMetaMetadataContributors() as $metadataContributor) {
            $this->parseLomContributor($metadataContributor, $root);
        }

        foreach ($this->nllom->getMetaMetadataSchemas() as $schema) {
            $node = $this->dom->createElement('metadatascheme');
            $value = $this->dom->createTextNode($schema);
            $node->appendChild($value);
            $root->appendChild($node);
        }

        if ($this->nllom->getMetaMetadataLanguage()) {
            $node = $this->dom->createElement('language', $this->nllom->getMetaMetadataLanguage());
            $root->appendChild($node);
        }
    }

    private function domSetTechnical(\DOMElement $element)
    {
        foreach ($this->nllom->getTechnicalFormats() as $technicalFormat) {
            $node = $this->dom->createElement('format', $technicalFormat);
            $element->appendChild($node);
        }

        if ($this->nllom->getTechnicalSize()) {
            $node = $this->dom->createElement('size', $this->nllom->getTechnicalSize());
            $element->appendChild($node);
        }

        foreach ($this->nllom->getTechnicalLocations() as $technicalLocation) {
            $node = $this->dom->createElement('location');
            $value = $this->dom->createTextNode($technicalLocation);
            $node->appendChild($value);
            $element->appendChild($node);
        }

        if ($this->nllom->getTechnicalRemarks()) {
            $this->parseLomLanguageString($this->nllom->getTechnicalRemarks(), 'installationremarks', $element);
        }

        if ($this->nllom->getTechnicalDuration()) {
            $this->parseLomDuration($this->nllom->getTechnicalDuration(), 'duration', $element);
        }
    }

    private function domSetEducational(\DOMElement $element)
    {
        if ($intType = $this->nllom->getEducationalInteractivityType()) {
            $this->parseLomTerm($intType, 'interactivitytype', $element);
        }

        foreach ($this->nllom->getEducationalLearningResourceTypes() as $row) {
            $this->parseLomTerm($row, 'learningresourcetype', $element);
        }

        if ($intLevel = $this->nllom->getEducationalInteractivityLevel()) {
            $this->parseLomTerm($intLevel, 'interactivitylevel', $element);
        }

        if ($density = $this->nllom->getEducationalSemanticDensity()) {
            $this->parseLomTerm($density, 'semanticdensity', $element);
        }

        foreach ($this->nllom->getEducationalIntendedUserRoles() as $row) {
            $this->parseLomTerm($row, 'intendedenduserrole', $element);
        }

        foreach ($this->nllom->getEducationalContexts() as $row) {
            $this->parseLomTerm($row, 'context', $element);
        }

        foreach ($this->nllom->getEducationalTypicalAgeRanges() as $row) {
            $node = $this->dom->createElement('typicalagerange');
            $node->appendChild($this->createLangstring($row));
            $element->appendChild($node);
        }

        if ($this->nllom->getEducationalDifficulty()) {
            $this->parseLomTerm($this->nllom->getEducationalDifficulty(), 'difficulty', $element);
        }

        if ($learningTime = $this->nllom->getEducationalTypicalLearningTime()) {
            $this->parseLomDuration($learningTime, 'typicallearningtime', $element);
        }

        foreach ($this->nllom->getEducationalDescriptions() as $row) {
            $this->parseLomLanguageString($row, 'description', $element);
        }

        foreach ($this->nllom->getEducationalLanguages() as $row) {
            $node = $this->dom->createElement('language');
            $value = $this->dom->createTextNode($row);
            $node->appendChild($value);
            $element->appendChild($node);
        }
    }

    private function domSetRights(\DomElement $element)
    {
        if ($cost = $this->nllom->getRightsCost()) {
            $this->parseLomTerm($cost, 'cost', $element);
        }

        if ($copyright = $this->nllom->getRightsCopyright()) {
            $this->parseLomTerm($copyright, 'copyrightandotherrestrictions', $element);
        }

        if ($this->nllom->getRightsDescription()) {
            $this->parseLomLanguageString($this->nllom->getRightsDescription(), 'description', $element);
        }
    }


    private function domAddRelations(\DOMNode $root)
    {
        foreach ($this->nllom->getRelations() as $relation) {
            /* @var $relation \Kennisnet\NLLOM\Library\LomRelation */

            $relationNode = $this->dom->createElement('relation');

            if ($relation->getKind()) {
                $this->parseLomTerm($relation->getKind(), 'kind', $relationNode);
            }

            $resource = $this->dom->createElement('resource');

            if ($relation->getDescription()) {
                $this->parseLomLanguageString($relation->getDescription(), 'description', $resource);
            }

            foreach ($relation->getResources() as $identifier) {
                $this->parseLomIdentifier($identifier, $resource);
            }

            $relationNode->appendChild($resource);

            $root->appendChild($relationNode);
        }
    }


    private function domAddClassifications(\DOMNode $root)
    {
        foreach ($this->nllom->getClassifications() as $classificationRow) {
            $classification = $this->dom->createElement('classification');

            $this->parseLomTerm($classificationRow->getPurpose(), 'purpose', $classification);

            $pathSources = [];

            foreach ($classificationRow->getTaxonPaths() as $taxonpath) {
                if (isset($pathSources[(string)$taxonpath->getSource()])) {
                    $node = $pathSources[(string)$taxonpath->getSource()];
                } else {
                    $node = $this->dom->createElement('taxonpath');

                    $src =  $this->dom->createElement('source');
                    $src->appendChild($this->createLangstring($taxonpath->getSource()));

                    $node->appendChild($src);
                }

                $pathSources[(string)$taxonpath->getSource()] = $node;

                foreach ($taxonpath->getTaxons() as $taxon) {
                    $t = $this->dom->createElement('taxon');
                    $id = $this->dom->createElement('id', $taxon->getId());

                    $t->appendChild($id);
                    $this->parseLomLanguageString($taxon->getTaxonEntry(), 'entry', $t);

                    $node->appendChild($t);
                }

                $classification->appendChild($node);
            }

            foreach($classificationRow->getKeywords() as $keyword){
                $kw = $this->dom->createElement('keyword');
                $kw->appendChild($this->createLangstring($keyword));
                $classification->appendChild($kw);
            }

            $root->appendChild($classification);
        }
    }

    private function createSourceValueElement($name, $key, $value)
    {
        $el = $this->dom->createElement($name);

        $src = $this->dom->createElement('source');
        $val = $this->dom->createElement('value');

        $src->appendChild($this->createLangstring($key));
        $val->appendChild($this->createLangstring($value));

        $el->appendChild($src);
        $el->appendChild($val);

        return $el;
    }

    private function createLangstring($value, $language = 'x-none')
    {
        $node = $this->dom->createElement('langstring');
        $value = $this->dom->createTextNode($value);
        $node->appendChild($value);
        $node->setAttribute('xml:lang', $language);
        return $node;
    }

    private function parseLomTerm(LomTerm $term, $name, \DOMElement $el)
    {
        $namedElement = $this->dom->createElement($name);

        $node = $this->dom->createElement('source');
        $node->appendChild($this->createLangstring($term->getSource()));
        $namedElement->appendChild($node);

        $node = $this->dom->createElement('value');
        $node->appendChild($this->createLangstring($term->getValue()));
        $namedElement->appendChild($node);

        $el->appendChild($namedElement);
    }

    private function parseLomLanguageString(LomMultiLanguage $strings, $name, \DOMElement $el)
    {
        $namedElement = $this->dom->createElement($name);

        foreach ($strings->getLanguageStrings() as $str) {
            $node = $this->createLangstring($str->getValue(), $str->getLanguageCode());
            $namedElement->appendChild($node);
        }

        $el->appendChild($namedElement);
    }

    private function parseLomContributor(LomContribute $lomContribute, \DOMElement $el)
    {
        $node = $this->dom->createElement('contribute');

        $this->parseLomTerm($lomContribute->getRole(), 'role', $node);


        foreach ($lomContribute->getEntities() as $entity) {
            $centity = $this->dom->createElement('centity');
            $vcard = $this->dom->createElement('vcard', $entity->getValue());
            $centity->appendChild($vcard);

            $node->appendChild($centity);
        }

        if ($lomContribute->getDateTime()) {
            $this->parseLomDateTime($lomContribute->getDateTime(), 'date', $node);
        }

        $el->appendChild($node);
    }

    private function parseLomDateTime(LomDateTime $lomDateTime, $name, \DOMElement $el)
    {
        $node = $this->dom->createElement($name);
        $dt = $this->dom->createElement('datetime', $lomDateTime->getDateTime());
        $node->appendChild($dt);

        $dtDesc = $lomDateTime->getDateTimeDescription();

        if ($dtDesc) {
            $this->parseLomLanguageString($dtDesc, 'description', $node);
        }

        $el->appendChild($node);
    }

    private function parseLomDuration(LomDuration $lomDateTime, $name, \DOMElement $el)
    {
        $node = $this->dom->createElement($name);
        $dt = $this->dom->createElement('datetime', $lomDateTime->getDuration());
        $node->appendChild($dt);

        $dtDesc = $lomDateTime->getDateTimeDescription();

        if ($dtDesc) {
            $this->parseLomLanguageString($dtDesc, 'description', $node);
        }

        $el->appendChild($node);
    }

    private function parseLomIdentifier(LomIdentifier $lomIdentifier, \DOMElement $el)
    {
        $catalogentry = $this->dom->createElement('catalogentry');

        $node = $this->dom->createElement('catalog', $lomIdentifier->getCatalog());
        $catalogentry->appendChild($node);

        $entry = $this->dom->createElement('entry');
        $entry->appendChild($this->createLangstring($lomIdentifier->getEntry()));

        $catalogentry->appendChild($entry);

        $el->appendChild($catalogentry);
    }
}