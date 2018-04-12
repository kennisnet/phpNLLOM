<?php

namespace Kennisnet\NLLOM;

use Kennisnet\NLLOM\Library\LomClassification;
use Kennisnet\NLLOM\Library\LomContribute;
use Kennisnet\NLLOM\Library\LomDuration;
use Kennisnet\NLLOM\Library\LomIdentifier;
use Kennisnet\NLLOM\Library\LomLanguageString;
use Kennisnet\NLLOM\Library\LomMultiLanguage;
use Kennisnet\NLLOM\Library\LomRelation;
use Kennisnet\NLLOM\Library\LomString;
use Kennisnet\NLLOM\Library\LomTerm;

/**
 * TODO: taxons onderelkaar horen hierarchisch geparsed te worden, dit verder uitzoeken voor leermiddeldomein!
 *
 * Class NLLOM
 * @package Kennisnet\NLLOM
 */
class Lom
{
    private $defaultLanguage = '';

    public function __construct(array $options = [])
    {
        $defaults = [
            'language' => 'nl'
        ];

        $options = array_merge($defaults, $options);

        $this->defaultLanguage = $options['language'];
    }


    //-----------------General-----------------

    /**
     * @var LomMultiLanguage
     */
    private $generalTitle;

    /**
     * @var LomMultiLanguage[]
     */
    private $generalDescriptions = [];

    /**
     * @var LomString[]
     */
    private $generalLanguages = [];

    /**
     * @var LomTerm
     */
    private $generalAggregationLevel;

    /**
     * @var LomIdentifier[]
     */
    private $generalIdentifiers = [];

    /**
     * @var LomMultiLanguage[]
     */
    private $generalKeywords = [];

    /**
     * @var LomMultiLanguage[]
     */
    private $generalCoverages = [];

    /**
     * @var LomTerm
     */
    private $generalStructure;


    //Lifecycle
    /**
     * @var LomString
     */
    private $lifecycleVersion;

    /**
     * @var LomTerm
     */
    private $lifecycleStatus;

    /**
     * @var LomContribute[]
     */
    private $lifecycleContributors = [];


    //MetaMetadate
    /**
     * @var LomContribute[]
     */
    private $metaMetadataContributors = [];

    /**
     * @var LomString
     */
    private $metaMetadataLanguage;

    /**
     * @var LomString[]
     */
    private $metaMetadataSchemas = [];

    /**
     * @var LomIdentifier[]
     */
    private $metaMetadataIdentifiers = [];

    //Technical
    /**
     * @var LomString[]
     */
    private $technicalFormats = [];

    /**
     * @var LomString
     */
    private $technicalSize;

    /**
     * @var LomString[]
     */
    private $technicalLocations = [];

    /**
     * @var LomMultiLanguage
     */
    private $technicalRemarks;

    /**
     * @var LomDuration
     */
    private $technicalDuration = [];

    //-----------------Educational-----------------------

    /**
     * @var LomTerm
     */
    private $educationalInteractivityType;

    /**
     * @var LomTerm[]
     */
    private $educationalLearningResourceTypes = [];

    /**
     * @var LomTerm
     */
    private $educationalInteractivityLevel;

    /**
     * @var LomTerm
     */
    private $educationalSemanticDensity;

    /**
     * @var LomTerm[]
     */
    private $educationalIntendedUserRoles = [];

    /**
     * @var LomTerm[]
     */
    private $educationalContexts = [];

    /**
     * @var LomString[]
     */
    private $educationalTypicalAgeRanges = [];

    /**
     * @var LomTerm
     */
    private $educationalDifficulty;

    /**
     * @var LomDuration
     */
    private $educationalTypicalLearningTime;

    /**
     * @var LomMultiLanguage[]
     */
    private $educationalDescriptions = [];

    /**
     * @var LomString[]
     */
    private $educationalLanguages = [];

    //------------Rights-----------------
    /**
     * @var LomTerm
     */
    private $rightsCost;

    /**
     * @var LomTerm
     */
    private $rightsCopyright;

    /**
     * @var LomMultiLanguage
     */
    private $rightsDescription;

    //Relations
    /**
     * @var LomRelation[]
     */
    private $relations = [];


    //Classification
    /**
     * @var LomClassification[]
     */
    private $classifications = [];

    /**
     * @param $keyword
     * @return $this
     */
    public function addGeneralKeyword(LomMultiLanguage $keyword)
    {
        $this->setDefaultLanguage($keyword);

        $this->generalKeywords[] = $keyword;
        return $this;
    }

    /**
     * @param LomIdentifier $catalogEntry
     * @return $this
     */
    public function addGeneralIdentifier(LomIdentifier $catalogEntry)
    {
        // Use double equalsigns for object comparison
        foreach ($this->getGeneralIdentifiers() as $identifier) {
            if ($identifier == $catalogEntry) {
                //Skip duplicate identifier
                return $this;
            }
        }

        $this->generalIdentifiers[] = $catalogEntry;
        return $this;
    }

    /**
     * @return LomIdentifier[]
     */
    public function getGeneralIdentifiers()
    {
        return $this->generalIdentifiers;
    }

    /**
     * @param $value
     * @return $this
     */
    public function addGeneralLanguage($value)
    {
        $this->generalLanguages[] = $value;
        return $this;
    }

    public function getGeneralLanguages()
    {
        return $this->generalLanguages;
    }

    /**
     * @return LomTerm
     */
    public function getGeneralAggregationLevel()
    {
        return $this->generalAggregationLevel;
    }

    /**
     * @return LomMultiLanguage[]
     */
    public function getGeneralKeywords()
    {
        return $this->generalKeywords;
    }

    /**
     * @param LomTerm $value
     * @return $this
     */
    public function setGeneralAggregationLevel(LomTerm $value)
    {
        $this->generalAggregationLevel = $value;
        return $this;
    }

    /**
     * @return LomMultiLanguage
     */
    public function getGeneralTitle()
    {
        return $this->generalTitle;
    }

    /**
     * @param LomMultiLanguage $language
     * @return $this
     */
    public function setGeneralTitle(LomMultiLanguage $language)
    {
        $this->setDefaultLanguage($language);

        $this->generalTitle = $language;
        return $this;
    }

    /**
     * @return LomMultiLanguage[]
     */
    public function getGeneralDescriptions()
    {
        return $this->generalDescriptions;
    }

    /**
     * @param LomMultiLanguage $description
     * @return $this
     */
    public function addGeneralDescription(LomMultiLanguage $description)
    {
        $this->setDefaultLanguage($description);

        $this->generalDescriptions[] = $description;
        return $this;
    }

    /**
     * @param LomString $lifecycleVersion
     * @return $this
     */
    public function setLifecycleVersion(LomString $lifecycleVersion)
    {
        $this->lifecycleVersion = $lifecycleVersion;
        return $this;
    }

    /**
     * @return LomMultiLanguage[]
     */
    public function getGeneralCoverages()
    {
        return $this->generalCoverages;
    }

    /**
     * @param LomMultiLanguage $generalCoverage
     * @return $this
     */
    public function addGeneralCoverage(LomMultiLanguage $generalCoverage)
    {
        $this->setDefaultLanguage($generalCoverage);

        $this->generalCoverages[] = $generalCoverage;
        return $this;
    }

    /**
     * @return LomTerm
     */
    public function getGeneralStructure()
    {
        return $this->generalStructure;
    }

    /**
     * @param LomTerm $generalStructure
     * @return $this
     */
    public function setGeneralStructure(LomTerm $generalStructure)
    {
        $this->generalStructure = $generalStructure;
        return $this;
    }


    //---------------lifecycle----------------

    /**
     * @return LomString
     */
    public function getLifecycleVersion()
    {
        return $this->lifecycleVersion;
    }

    /**
     * @return LomTerm
     */
    public function getLifecycleStatus()
    {
        return $this->lifecycleStatus;
    }

    /**
     * @param LomTerm $lifecycleStatus
     * @return $this
     */
    public function setLifecycleStatus(LomTerm $lifecycleStatus)
    {
        $this->lifecycleStatus = $lifecycleStatus;
        return $this;
    }

    /**
     * @return LomContribute[]
     */
    public function getLifecycleContributors()
    {
        return $this->lifecycleContributors;
    }

    public function addLifecycleContributor(LomContribute $contributor)
    {
        if ($contributor->getDateTime()) {
            if ($contributor->getDateTime()->getDateTimeDescription()) {
                $this->setDefaultLanguage($contributor->getDateTime()->getDateTimeDescription());
            }
        }

        $this->lifecycleContributors[] = $contributor;
        return $this;
    }

    /**
     * @return LomContribute[]
     */
    public function getMetaMetadataContributors()
    {
        return $this->metaMetadataContributors;
    }

    public function addMetaMetadataContributor(LomContribute $contributor)
    {
        $this->metaMetadataContributors[] = $contributor;
        return $this;
    }

    /**
     * @param LomIdentifier $catalogEntry
     * @return $this
     */
    public function addMetaMetadataIdentifier(LomIdentifier $catalogEntry)
    {
        $this->metaMetadataIdentifiers[] = $catalogEntry;
        return $this;
    }

    /**
     * @return LomIdentifier[]
     */
    public function getMetaMetadataIdentifiers()
    {
        return $this->metaMetadataIdentifiers;
    }


    /**
     * @return LomString
     */
    public function getMetaMetadataLanguage()
    {
        return $this->metaMetadataLanguage;
    }

    /**
     * @param LomString $value
     * @return $this
     */
    public function setMetaMetadataLanguage(LomString $value)
    {
        $this->metaMetadataLanguage = $value;
        return $this;
    }

    /**
     * @return LomString[]
     */
    public function getMetaMetadataSchemas()
    {
        return $this->metaMetadataSchemas;
    }

    /**
     * @param LomString $value
     * @return $this
     */
    public function addMetaMetadataSchema(LomString $value)
    {
        $this->metaMetadataSchemas[] = $value;
        return $this;
    }

    //-------------Technical-----------------------------

    /**
     * @return LomString[]
     */
    public function getTechnicalFormats()
    {
        return $this->technicalFormats;
    }

    /**
     * @param LomString $value
     * @return $this
     */
    public function addTechnicalFormat(LomString $value)
    {
        $this->technicalFormats[] = $value;
        return $this;
    }

    /**
     * @return LomString
     */
    public function getTechnicalSize()
    {
        return $this->technicalSize;
    }

    /**
     * @param LomString $value
     * @return $this
     * @throws \Exception
     */
    public function setTechnicalSize(LomString $value)
    {
        if ((int)$value->getValue() === 0) {
            throw new \Exception('Invalid value');
        }

        $this->technicalSize = $value;
        return $this;
    }

    /**
     * @return LomString[]
     */
    public function getTechnicalLocations()
    {
        return $this->technicalLocations;
    }

    /**
     * @param LomString $value
     * @return $this
     */
    public function addTechnicalLocation(LomString $value)
    {
        $this->technicalLocations[] = $value;
        return $this;
    }

    /**
     * @param LomMultiLanguage $language
     * @return $this
     */
    public function setTechnicalRemarks(LomMultiLanguage $language)
    {
        $this->setDefaultLanguage($language);

        $this->technicalRemarks = $language;
        return $this;
    }

    /**
     * @return LomMultiLanguage
     */
    public function getTechnicalRemarks()
    {
        return $this->technicalRemarks;
    }

    /**
     * @return LomDuration
     */
    public function getTechnicalDuration()
    {
        return $this->technicalDuration;
    }

    /**
     * @param LomDuration $lomDateTime
     * @return $this
     */
    public function setTechnicalDuration(LomDuration $lomDateTime)
    {
        if ($lomDateTime->getDateTimeDescription()) {
            $this->setDefaultLanguage($lomDateTime->getDateTimeDescription());
        }

        $this->technicalDuration = $lomDateTime;
        return $this;
    }


    //-------------Educational-----------------------------

    /**
     * @return LomTerm
     */
    public function getEducationalInteractivityType()
    {
        return $this->educationalInteractivityType;
    }

    /**
     * @param $educationalInteractivityType
     * @return $this
     */
    public function setEducationalInteractivityType($educationalInteractivityType)
    {
        $this->educationalInteractivityType = $educationalInteractivityType;
        return $this;
    }

    /**
     * @return LomTerm[]
     */
    public function getEducationalLearningResourceTypes()
    {
        return $this->educationalLearningResourceTypes;
    }

    public function addEducationalLearningResourceType(LomTerm $lomTerm)
    {
        $this->educationalLearningResourceTypes[] = $lomTerm;
        return $this;
    }

    /**
     * @return LomTerm
     */
    public function getEducationalInteractivityLevel()
    {
        return $this->educationalInteractivityLevel;
    }

    /**
     * @param $educationalInteractivityLevel
     * @return $this
     */
    public function setEducationalInteractivityLevel($educationalInteractivityLevel)
    {
        $this->educationalInteractivityLevel = $educationalInteractivityLevel;
        return $this;
    }

    /**
     * @return LomTerm
     */
    public function getEducationalSemanticDensity()
    {
        return $this->educationalSemanticDensity;
    }

    /**
     * @param $educationalSemanticDensity
     * @return $this
     */
    public function setEducationalSemanticDensity($educationalSemanticDensity)
    {
        $this->educationalSemanticDensity = $educationalSemanticDensity;
        return $this;
    }

    /**
     * @return LomTerm[]
     */
    public function getEducationalIntendedUserRoles()
    {
        return $this->educationalIntendedUserRoles;
    }


    public function addEducationalIntendedUserRole(LomTerm $value)
    {
        $this->educationalIntendedUserRoles[] = $value;
        return $this;
    }

    /**
     * @return LomTerm[]
     */
    public function getEducationalContexts()
    {
        return $this->educationalContexts;
    }

    /**
     * @param LomTerm $lomTerm
     * @return $this
     */
    public function addEducationalContext(LomTerm $lomTerm)
    {
        $this->educationalContexts[] = $lomTerm;
        return $this;
    }

    /**
     * @return LomString[]
     */
    public function getEducationalTypicalAgeRanges()
    {
        return $this->educationalTypicalAgeRanges;
    }

    /**
     * @param LomString $value
     * @return $this
     */
    public function addEducationalTypicalAgeRange(LomString $value)
    {
        $this->educationalTypicalAgeRanges[] = $value;
        return $this;
    }

    /**
     * @return LomTerm
     */
    public function getEducationalDifficulty()
    {
        return $this->educationalDifficulty;
    }

    /**
     * @param LomTerm $value
     * @return $this
     */
    public function setEducationalDifficulty(LomTerm $value)
    {
        $this->educationalDifficulty = $value;
        return $this;
    }

    /**
     * @return LomDuration
     */
    public function getEducationalTypicalLearningTime()
    {
        return $this->educationalTypicalLearningTime;
    }

    /**
     * @param LomDuration $value
     * @return $this
     */
    public function setEducationalTypicalLearningTime(LomDuration $value)
    {
        if ($value->getDateTimeDescription()) {
            $this->setDefaultLanguage($value->getDateTimeDescription());
        }

        $this->educationalTypicalLearningTime = $value;
        return $this;
    }

    /**
     * @return LomMultiLanguage[]
     */
    public function getEducationalDescriptions()
    {
        return $this->educationalDescriptions;
    }

    /**
     * @param LomMultiLanguage $description
     * @return $this
     */
    public function addEducationalDescription(LomMultiLanguage $description)
    {
        $this->setDefaultLanguage($description);

        $this->educationalDescriptions[] = $description;
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function addEducationalLanguage($value)
    {
        $this->educationalLanguages[] = $value;
        return $this;
    }

    public function getEducationalLanguages()
    {
        return $this->educationalLanguages;
    }


    //----------------Rights-----------------------------

    /**
     * @return LomTerm
     */
    public function getRightsCost()
    {
        return $this->rightsCost;
    }

    /**
     * @param LomTerm $value
     * @return $this
     */
    public function setRightsCost(LomTerm $value)
    {
        $this->rightsCost = $value;
        return $this;
    }

    /**
     * @return LomTerm
     */
    public function getRightsCopyright()
    {
        return $this->rightsCopyright;
    }

    /**
     * @param LomTerm $value
     * @return $this
     */
    public function setRightsCopyright(LomTerm $value)
    {
        $this->rightsCopyright = $value;
        return $this;
    }

    /**
     * @return LomMultiLanguage
     */
    public function getRightsDescription()
    {
        return $this->rightsDescription;
    }

    /**
     * @param LomMultiLanguage $value
     * @return $this
     */
    public function setRightsDescription(LomMultiLanguage $value)
    {
        $this->setDefaultLanguage($value);

        $this->rightsDescription = $value;
        return $this;
    }

    /**
     * @return LomRelation[]
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param LomRelation $lomRelation
     * @return $this
     */
    public function addRelation(LomRelation $lomRelation)
    {
        if ($lomRelation->getDescription()) {
            $this->setDefaultLanguage($lomRelation->getDescription());
        }

        $this->relations[] = $lomRelation;
        return $this;
    }

    /**
     * @return LomClassification[]
     */
    public function getClassifications()
    {
        return $this->classifications;
    }

    /**
     * @param LomClassification $classification
     * @return $this
     */
    public function addClassification(LomClassification $classification)
    {
        foreach ($classification->getTaxonPaths() as $path) {
            foreach ($path->getTaxons() as $taxon) {
                $this->setDefaultLanguage($taxon->getTaxonEntry());
            }
        }

        $this->classifications[] = $classification;
        return $this;
    }

    /**
     * @param LomMultiLanguage $multi
     */
    private function setDefaultLanguage(LomMultiLanguage $multi)
    {
        foreach ($multi->getLanguageStrings() as $lomLanguageString) {
            if (!$lomLanguageString->getLanguageCode()) {
                $lomLanguageString->setLanguageCode($this->defaultLanguage);
            }
        }
    }
}
