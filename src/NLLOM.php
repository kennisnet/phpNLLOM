<?php
namespace Kennisnet\NLLOM;

class NLLOM
{
    const XMLNS = "http://ltsc.ieee.org/xsd/LOM";
    const XSI_SCHEMALOCATION = "http://ltsc.ieee.org/xsd/LOM http://ltsc.ieee.org/xsd/lomv1.0/lom.xsd";

    //General
    private $generalTitle;
    private $generalDescription;
    private $generalLanguages = [];
    private $generalAggregationLevel;
    private $generalIdentifier;
    private $generalIdentifiers = [];
    private $generalKeywords = [];

    //Lifecycle
    private $lifecycleVersion;
    private $lifecycleStatus = [];
    private $lifecycleAuthor = [];
    private $lifecyclePublisher = [];

    //Metametadate
    private $metametadataCreator = [];
    private $metametadataValidator = [];
    private $metametadataLanguage;

    //Technical
    private $technicalFormat;
    private $technicalSize;
    private $technicalLocation;
    private $technicalDuration = [];

    //Educational
    private $educationalLearningResourceTypes = [];
    private $educationalIntendedUserRoles = [];
    private $educationalContexts = [];
    private $educationalTypicalAgeRanges = [];

    //Rights
    private $rightsCost;
    private $rightsCopyright = [];
    private $rightsDescription = [];

    //Relations
    private $relations = [];

    private $publishers = [];


    //Classification
    private $classifications = [];

    /**
     * @var \DOMDocument
     */
    private $dom;

    private $options = [];

    public function __construct($options = [])
    {
        $defaults = [
            'validate' => true,
            'debug' => false,
            'lom_version' => 'LOMv1.0',
            'lom_schema' => 'nl_lom_v1p0',
            'preserve_whitespace' => true,
            'format_output' => true
        ];

        $this->options = array_merge($defaults, $options);
    }

    /**
     * @param $keyword
     */
    public function addGeneralKeyword($keyword)
    {
        $this->generalKeywords[] = $keyword;
    }

    /**
     * Add identifier
     *
     * Example: 'uri', 'urn:uuid:foo-bar'
     *
     * @param $key
     * @param $value
     */
    public function addGeneralIdentifier($key, $value)
    {
        $this->generalIdentifiers[] = [
            'key' => $key,
            'value' => $value
        ];
    }

    /**
     * Set identifier
     *
     * This will be the first identifier in the LOM
     *
     * Example: 'uri', 'urn:uuid:foo-bar'
     *
     * @param $key
     * @param $value
     */
    public function setGeneralIdentifier($key, $value)
    {
        $this->generalIdentifier = [
            'key' => $key,
            'value' => $value
        ];
    }

    public function addGeneralLanguage($value)
    {
        $this->generalLanguages[] = $value;
    }

    /**
     * Add relation
     *
     * @param $key
     * @param $value
     * @param string $description
     */
    public function addRelation($key, $value, $description = '')
    {
        $this->relations[] = [
            'key' => $key,
            'value' => $value
        ];
    }

    public function setGeneralAggregationLevel($value)
    {
        $this->generalAggregationLevel = $value;
    }

    /**
     * @param $title
     */
    public function setGeneralTitle($title)
    {
        $this->generalTitle = $title;
    }

    /**
     * @param $description
     */
    public function setGeneralDescription($description)
    {
        $this->generalDescription = $description;
    }

    /**
     * @param mixed $lifecycleVersion
     */
    public function setLifecycleVersion($lifecycleVersion)
    {
        $this->lifecycleVersion = $lifecycleVersion;
    }

    /**
     * @param array $lifecycleStatus
     */
    public function setLifecycleStatus($lifecycleStatus)
    {
        $this->lifecycleStatus = $lifecycleStatus;
    }

    /**
     * Set author
     *
     * $description is only valid in combination with a datetime value
     *
     * @param $vcard
     * @param \DateTime $datetime
     * @param string $description
     * @param string $language
     */
    public function setLifecycleAuthor(\DateTime $datetime, $vcard = '', $description = '', $language = 'nl')
    {
        $this->lifecycleAuthor = [
            'vcard' => $vcard,
            'datetime' => $datetime,
            'description' => $description,
            'language' => $language
        ];
    }

    //Alias
    public function setAuthor(\DateTime $datetime, $vcard = '', $description = '', $language = 'nl')
    {
        $this->setLifecycleAuthor($datetime, $vcard, $description, $language);
    }

    /**
     * Set publisher
     *
     * $description is only valid in combination with a datetime value
     *
     * @param \DateTime $datetime
     * @param string $vcard
     * @param string $description
     * @param string $language
     */
    public function setLifecyclePublisher(\DateTime $datetime, $vcard = '', $description = '', $language = 'nl')
    {
        $this->lifecyclePublisher = [
            'vcard' => $vcard,
            'datetime' => $datetime,
            'description' => $description,
            'language' => $language
        ];
    }

    //Alias
    public function setPublisher(\DateTime $datetime, $vcard = '', $description = '', $language = 'nl')
    {
        $this->setLifecyclePublisher($datetime, $vcard, $description, $language);
    }


    /**
     * Set creator
     *
     * $description is only valid in combination with a datetime value
     *
     * @param $vcard
     * @param \DateTime $datetime
     * @param string $description
     * @param string $language
     */
    public function setMetametadataCreator(\DateTime $datetime, $vcard = '', $description = '', $language = 'nl')
    {
        $this->metametadataCreator = [
            'vcard' => $vcard,
            'datetime' => $datetime,
            'description' => $description,
            'language' => $language
        ];
    }

    //Alias
    public function setCreator(\DateTime $datetime, $vcard = '', $description = '', $language = 'nl')
    {
        $this->setMetametadataCreator($datetime, $vcard, $description, $language);
    }

    /**
     * Set validator
     *
     * $description is only valid in combination with a datetime value
     *
     * @param $vcard
     * @param \DateTime $datetime
     * @param string $description
     * @param string $language
     */
    public function setMetametadataValidator(\DateTime $datetime, $vcard = '', $description = '', $language = 'nl')
    {
        $this->metametadataValidator = [
            'vcard' => $vcard,
            'datetime' => $datetime,
            'description' => $description,
            'language' => $language
        ];
    }

    //Alias
    public function setValidator(\DateTime $datetime, $vcard = '', $description = '', $language = 'nl')
    {
        $this->setMetametadataValidator($datetime, $vcard, $description, $language);
    }


    public function setMetametadataLanguage($value)
    {
        $this->metametadataLanguage = $value;
    }

    /**
     * @param $value
     */
    public function setTechnicalFormat($value)
    {
        $this->technicalFormat = $value;
    }

    /**
     * @param $value
     */
    public function setTechnicalSize($value)
    {
        $this->technicalSize = $value;
    }

    /**
     * @param $value
     */
    public function setTechnicalLocation($value)
    {
        $this->technicalLocation = $value;
    }

    /**
     * @param $datetime
     * @param string $description
     * @param string $language
     */
    public function setTechnicalDuration($datetime, $description = '', $language = 'nl')
    {
        $this->technicalDuration = [
            'datetime' => $datetime,
            'description' => $description,
            'language' => $language
        ];
    }

    public function addEducationalLearningResourceType($key, $value)
    {
        $this->educationalLearningResourceTypes[] = [
            'key' => $key,
            'value' => $value
        ];
    }

    public function addEducationaIntendedUserRole($value)
    {
        $this->educationalIntendedUserRoles[] = $value;
    }

    public function addEducationalContext($key, $value)
    {
        $this->educationalContexts[] = [
            'key' => $key,
            'value' => $value
        ];
    }

    public function addEducationalTypicalAgeRange($value)
    {
        $this->educationalTypicalAgeRanges[] = $value;
    }

    public function setRightsCost($value)
    {
        $this->rightsCost = $value;
    }

    public function setRightsCopyright($source, $value)
    {
        $this->rightsCopyright = [
            'key' => $source,
            'value' => $value
        ];
    }

    public function setRightsDescription($value, $language = 'nl')
    {
        $this->rightsDescription = [
            'value' => $value,
            'language' => $language
        ];
    }

    /**
     * Add classification
     *
     * Datastructure:
     *
     * $purpose = [
     *   'source' => '',
     *   'value' => ''
     *   ];
     *
     * $taxonpaths = [
     *  'source' => '',
     *  'taxons' => [
     *    'id' => '',
     *    'value' => '',
     *    'language' => 'nl'
     *   ]
     * ];
     *
     *
     * @param array $purpose
     * @param array $taxonpaths
     */
    public function addClassification(array $purpose, array $taxonpaths = [])
    {
        $this->classifications[] = [
            'purpose' => $purpose,
            'taxonpaths' => $taxonpaths,
        ];
    }


    /**
     * Get DOM
     *
     * @return \DOMDocument
     * @throws \Exception
     */
    public function getDom()
    {
        $domDocument = new \DOMDocument('1.0', 'UTF-8');

        $this->dom = $domDocument;

        $domDocument->formatOutput = $this->options['format_output'];
        $domDocument->preserveWhiteSpace = $this->options['preserve_whitespace'];

        $root = $domDocument->createElementNS(self::XMLNS, 'lom');
        $root = $domDocument->appendChild($root);
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation', self::XSI_SCHEMALOCATION);

        $general = $domDocument->createElement('general');
        $this->domSetTitle($general);
        $this->domAddIdentifiers($general);
        $this->domLanguages($general);
        $this->domSetDescription($general);
        $this->domAddKeywords($general);
        $this->domSetAggregationLevel($general);
        $root->appendChild($general);

        $this->domSetLifecycle($root);

        $metametadata = $domDocument->createElement('metametadata');
        $this->domSetMetametadata($metametadata);
        $root->appendChild($metametadata);

        $technical = $domDocument->createElement('technical');
        $this->domSetTechnical($technical);
        $root->appendChild($technical);

        $educational = $domDocument->createElement('educational');
        $this->domSetEducational($educational);
        $root->appendChild($educational);

        $rights = $domDocument->createElement('rights');
        $this->domSetRights($rights);
        $root->appendChild($rights);

        $this->domAddClassifications($root);

        //$this->domAddRelations($domDocument, $root);

        return $domDocument;
    }

    /**
     * Get XML from DOM
     *
     * @return string
     * @throws \Exception
     */
    public function saveAsXML(){
      $domDocument = $this->getDom();
      $xml = $domDocument->saveXML();

      if ($this->options['debug']) {
          self::writeDebug($xml);
      }

      if ($this->options['validate']) {
          Validator::validate($xml);

          if ($this->options['debug']) self::writeDebug('Validation success');
      }

      return $xml;
    }

    private function domAddIdentifiers(\DOMElement $general)
    {
        $addDom = function ($identifier) use ($general) {
            $catalogentry = $this->dom->createElement('catalogentry');

            $node = $this->dom->createElement('catalog', $identifier["key"]);
            $catalogentry->appendChild($node);

            $entry = $this->dom->createElement('entry');
            $entry->appendChild($this->createLangstring($identifier["value"]));

            $catalogentry->appendChild($entry);

            $general->appendChild($catalogentry);
        };

        $addDom($this->generalIdentifier);

        foreach ($this->generalIdentifiers as $identifier) {
            $addDom($identifier);
        }
    }

    /**
     * Add keywords to DomElement
     *
     * @param \DOMElement $element
     * @param string $language
     */
    private function domAddKeywords(\DOMElement $element, $language = 'nl')
    {
        foreach ($this->generalKeywords as $value) {

            $keyword = $this->dom->createElement('keyword');
            $keyword->appendChild($this->createLangstring($value, $language));

            $element->appendChild($keyword);
        }
    }

    /**
     * @param \DOMElement $element
     */
    private function domLanguages(\DOMElement $element)
    {
        foreach ($this->generalLanguages as $language) {
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
        if ($this->generalAggregationLevel) {

            $level = $this->dom->createElement('aggregationlevel');

            $node = $this->dom->createElement('source');
            $node->appendChild($this->createLangstring($this->options['lom_version']));
            $level->appendChild($node);

            $node = $this->dom->createElement('value');
            $node->appendChild($this->createLangstring($this->generalAggregationLevel));
            $level->appendChild($node);

            $element->appendChild($level);
        }

    }

    private function domSetLifecycle(\DomNode $root)
    {
        $lifecycle = $this->dom->createElement('lifecycle');

        if ($this->lifecycleVersion) {
            $node = $this->dom->createElement('version');
            $node->appendChild($this->createLangstring($this->lifecycleVersion));
            //$node = $this->createLangstring($this->lifecycleVersion);
            $lifecycle->appendChild($node);
        }

        if ($this->lifecycleStatus) {
            $node = $this->dom->createElement('status');
            $src = $this->dom->createElement('source');
            $val = $this->dom->createElement('value');

            $src->appendChild($this->createLangstring($this->options['lom_version']));
            $val->appendChild($this->createLangstring($this->lifecycleStatus));

            $node->appendChild($src);
            $node->appendChild($val);

            $lifecycle->appendChild($node);
        }

        if ($this->lifecycleAuthor) {
            $node = $this->createContributor('author', $this->lifecycleAuthor);
            $lifecycle->appendChild($node);
        }

        if ($this->lifecyclePublisher) {
            $node = $this->createContributor('publisher', $this->lifecyclePublisher);
            $lifecycle->appendChild($node);
        }

        if ($lifecycle->hasChildNodes()) {
            $root->appendChild($lifecycle);
        }
    }

    private function domSetMetametadata(\DomNode $root)
    {
        if ($this->metametadataCreator) {
            $node = $this->createContributor('creator', $this->metametadataCreator);
            $root->appendChild($node);
        }

        if ($this->metametadataValidator) {
            $node = $this->createContributor('validator', $this->metametadataValidator);
            $root->appendChild($node);
        }

        $node = $this->dom->createElement('metadatascheme', $this->options['lom_version']);
        $root->appendChild($node);

        $node = $this->dom->createElement('metadatascheme', $this->options['lom_schema']);
        $root->appendChild($node);

        if ($this->metametadataLanguage) {
            $node = $this->dom->createElement('language', $this->metametadataLanguage);
            $root->appendChild($node);
        }
    }

    private function domSetTechnical(\DOMElement $element)
    {
        $node = $this->dom->createElement('format', $this->technicalFormat);
        $element->appendChild($node);

        if ($this->technicalSize) {
            $node = $this->dom->createElement('size', $this->technicalSize);
            $element->appendChild($node);
        }

        $node = $this->dom->createElement('location');
        $value = $this->dom->createTextNode($this->technicalLocation);
        $node->appendChild($value);
        $element->appendChild($node);

        if ($this->technicalDuration) {
            $node = $this->dom->createElement('duration');
            $dt = $this->dom->createElement('datetime', $this->technicalDuration['datetime']);
            $desc = $this->dom->createElement('description');
            $desc->appendChild($this->createLangstring($this->technicalDuration['description'], $this->technicalDuration['language']));

            $node->appendChild($dt);
            $node->appendChild($desc);

            $element->appendChild($node);
        }
    }

    private function domSetEducational(\DOMElement $element)
    {
        $addElement = function ($name, $key, $value) use ($element) {
            $role = $this->dom->createElement($name);

            $src = $this->dom->createElement('source');
            $val = $this->dom->createElement('value');

            $src->appendChild($this->createLangstring($key));
            $val->appendChild($this->createLangstring($value));

            $role->appendChild($src);
            $role->appendChild($val);

            $element->appendChild($role);
        };


        foreach ($this->educationalLearningResourceTypes as $row) {
            $addElement('learningresourcetype', $row['key'], $row['value']);
        }

        foreach ($this->educationalIntendedUserRoles as $row) {
            $addElement('intendedenduserrole', $this->options['lom_version'], $row);
        }

        foreach ($this->educationalContexts as $row) {
            $addElement('context', $row['key'], $row['value']);
        }

        foreach ($this->educationalTypicalAgeRanges as $row) {
            $node = $this->dom->createElement('typicalagerange');
            $node->appendChild($this->createLangstring($row));
            $element->appendChild($node);
        }
    }

    private function domSetRights(\DomElement $element)
    {
        $node = $this->createSourceValueElement('cost', $this->options['lom_version'], $this->rightsCost);
        $element->appendChild($node);

        $node = $this->createSourceValueElement('copyrightandotherrestrictions', $this->rightsCopyright['key'], $this->rightsCopyright['value']);
        $element->appendChild($node);

        if ($this->rightsDescription) {
            $node = $this->dom->createElement('description');
            $node->appendChild($this->createLangstring($this->rightsDescription['value'], $this->rightsDescription['language']));
            $element->appendChild($node);
        }
    }


    private function domAddRelations(\DOMElement $element)
    {
        foreach ($this->relations as $relation) {

            $node = $this->dom->createElement('relation');

            $kind = $this->createSourceValueElement('kind', 1, 1);
            $node->appendChild($kind);

            $resource = $this->dom->createElement('resource');


            $template = <<<XML
<relation>
  <kind>
    <source>
      <langstring xml:lang="x-none">http://purl.edustandaard.nl/relation_kind_nllom_20131211</langstring>
    </source>
    <value>
      <langstring xml:lang="x-none">{$relation['key']}</langstring>
    </value>
  </kind>
  <resource>
XML;

            if ($relation['description']) {
                $template .= <<<XML
    <description>
      <langsting xml:lang="x-none">application/pdf</langstring>
    </description>
XML;
            }

            $template .= <<<XML
    <catalogentry>
      <catalog>URI</catalog>
      <entry>
        <langstring xml:lang="x-none">{$relation['value']}</langstring>
      </entry>
    </catalogentry>
  </resource>
</relation>
XML;

            $element->appendChild($node);

        }
    }


    private function domAddClassifications(\DOMNode $root)
    {
        foreach ($this->classifications as $row) {

            $classification = $this->dom->createElement('classification');

            $purpose = $this->createSourceValueElement('purpose', $row['purpose']['source'], $row['purpose']['value']);
            $classification->appendChild($purpose);

            foreach ($row['taxonpaths'] as $taxonpath) {
                $node = $this->dom->createElement('taxonpath');
                $src =  $this->dom->createElement('source');
                $src->appendChild($this->createLangstring($taxonpath['source']));
                $node->appendChild($src);

                foreach ($taxonpath['taxons'] as $taxon) {
                    $t = $this->dom->createElement('taxon');
                    $id = $this->dom->createElement('id', $taxon['id']);
                    $entry = $this->dom->createElement('entry');
                    $entry->appendChild($this->createLangstring($taxon['value'], $taxon['language']));

                    $t->appendChild($id);
                    $t->appendChild($entry);

                    $node->appendChild($t);
                }

                $classification->appendChild($node);
            }

            $root->appendChild($classification);
        }
    }

    private function domSetTitle(\DOMElement $general, $language = 'nl')
    {
        $title = $this->dom->createElement('title');

        $node = $this->dom->createElement('langstring', $this->generalTitle);
        $node->setAttribute('xml:lang', $language);

        $title->appendChild($node);

        $general->appendChild($title);
    }

    private function domSetDescription(\DOMElement $general, $language = 'nl')
    {
        if ($this->generalDescription) {
            $title = $this->dom->createElement('description');

            $node = $this->dom->createElement('langstring', $this->generalDescription);
            $node->setAttribute('xml:lang', $language);

            $title->appendChild($node);

            $general->appendChild($title);
        }

    }

    private function createContributor($roleValue, array $data)
    {
        $node = $this->dom->createElement('contribute');
        $role = $this->dom->createElement('role');

        $src = $this->dom->createElement('source');
        $val = $this->dom->createElement('value');

        $src->appendChild($this->createLangstring($this->options['lom_version']));
        $val->appendChild($this->createLangstring($roleValue));

        $role->appendChild($src);
        $role->appendChild($val);

        $node->appendChild($role);

        if ($data['vcard']) {
            $centity = $this->dom->createElement('centity');
            $vcard = $this->dom->createElement('vcard', $data['vcard']);
            $centity->appendChild($vcard);

            $node->appendChild($centity);
        }

        if ($data['datetime']) {
            $date = $this->dom->createElement('date');
            $dt = $this->dom->createElement('datetime', $data['datetime']->format('Y-m-d'));
            $date->appendChild($dt);

            if ($data['description']) {
                $desc = $this->dom->createElement('description');
                $desc->appendChild($this->createLangstring($data['description'], $data['language']));
                $date->appendChild($desc);
            }

            $node->appendChild($date);
        }

        return $node;
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

    private static function writeDebug($msg)
    {
        echo $msg . PHP_EOL;
    }

}
