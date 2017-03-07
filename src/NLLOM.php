<?php
namespace Kennisnet;

$mapping = [
    'http://www.imsglobal.org/xsd/imsmd_v1p2p4.xsd' => 'imsmd_v1p2p4.xsd',
    'http://www.w3.org/2001/xml.xsd' => 'xml.xsd',
    'https://www.w3.org/2001/03/xml.xsd' => 'xml_3.xsd',
    'http://www.w3.org/2001/03/xml.xsd' => 'xml_3.xsd',
];

libxml_set_external_entity_loader(
    function ($public, $system, $context) use ($mapping) {

        if (is_file($system)) return $system;

        $path = realpath(__DIR__.'/xsd/'.basename($system));

        if (!is_file($path)) {
            throw new \Exception($path . ' not found');
        }

        return realpath(__DIR__.'/xsd/'.basename($system));
    }
);


class NLLOM
{
    const XMLNS = "http://www.imsglobal.org/xsd/imsmd_v1p2";
    const XMLNS_XSI = "http://www.w3.org/2001/XMLSchema-instance";
    const XSI_SCHEMALOCATION = "http://www.imsglobal.org/xsd/imsmd_v1p2 http://www.imsglobal.org/xsd/imsmd_v1p2p4.xsd";
    const FORMATTING = true;
    const PRESERVE_WS = false;

    private $validate = true;
    private $debug = false;

    private $title;
    private $description;
    private $format;
    private $relations = [];
    private $identifiers = [];
    private $keywords = [];
    private $publishers = [];

    public function __construct($validate = true, $debug = false)
    {
        $this->validate = $validate;
        $this->debug = $debug;
    }

    /**
     * Add keyword to <general>
     *
     * @param $keyword
     */
    public function addGeneralKeyword($keyword)
    {
        $this->keywords[] = $keyword;
    }

    /**
     * Add identifier
     *
     * Example: 'uri', 'urn:uuid:foo-bar'
     *
     * @param $key
     * @param $value
     */
    public function addIdentifier($key, $value)
    {
        $this->identifiers[] = [
            'key' => $key,
            'value' => $value
        ];
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

    /**
     * @param $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }


    /**
     * Get XML from DOM
     *
     * @return string
     * @throws \Exception
     */
    public function saveAsXML()
    {
        $domDocument = new \DOMDocument('1.0', 'UTF-8');
        $domDocument->formatOutput = self::FORMATTING;
        $domDocument->preserveWhiteSpace = self::PRESERVE_WS;

        $root = $domDocument->createElementNS(self::XMLNS, 'lom');
        $root = $domDocument->appendChild($root);
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation', self::XSI_SCHEMALOCATION);

        $general = $domDocument->createElement('general');
        $this->domSetTitle($domDocument, $general);
        //$this->domSetUUID($domDocument, $general);
        $this->domAddIdentifiers($domDocument, $general);

        $this->domSetDescription($domDocument, $general);
        $this->domAddKeywords($domDocument, $general);
        $root->appendChild($general);

        $this->domAddRelations($domDocument, $root);


//        $record->setTitle($row['title']);
//        $record->addIdentifier('UUID');
//        $record->setLanguage('nl');
//
//        if ($row['description']) {
//            $record->setDescription($row['description']);
//        }
//
//

        $metametadata = $domDocument->createElement('metametadata');
        $root->appendChild($metametadata);

        $technical = $domDocument->createElement('technical');
        $this->domSetFormat($domDocument, $technical);
        $root->appendChild($technical);

        $educational = $domDocument->createElement('educational');
        $root->appendChild($educational);

        $rights = $domDocument->createElement('rights');
        $root->appendChild($rights);

        $xml = $domDocument->saveXML();

        if ($this->debug) {
            self::writeDebug($xml);
        }

        if ($this->validate) {
            $domDocumentValidate = new \DOMDocument('1.0', 'UTF-8');
            $domDocumentValidate->loadXML($xml);

            if (!$domDocumentValidate->schemaValidate('xsd/imsmd_v1p2p4.xsd')) {
                throw new \Exception('Validation failed');
            } else {
                if ($this->debug) self::writeDebug('Validation success');
            }
        }

        return $xml;
    }

    private function domAddIdentifiers(\DOMDocument $document, \DOMElement $general)
    {
        foreach ($this->identifiers as $identifier) {
            //Insert identifier XML
            $template = <<< XML
<catalogentry>
    <catalog>{$identifier["key"]}</catalog>
    <entry>
        <langstring xml:lang="x-none">{$identifier["value"]}</langstring>
    </entry>
</catalogentry>
XML;

            $fragment = $document->createDocumentFragment();
            $fragment->appendXML($template);
            $general->appendChild($fragment);
        }
    }

    private function domAddKeywords(\DOMDocument $document, \DOMElement $general)
    {
        foreach ($this->keywords as $keyword) {

            $template = <<<XML
<keyword>
    <langstring xml:lang="nl">$keyword</langstring>
</keyword>
XML;

            $fragment = $document->createDocumentFragment();
            $fragment->appendXML($template);
            $general->appendChild($fragment);
        }

    }

    private function domAddRelations(\DOMDocument $document, \DOMElement $element)
    {
        foreach ($this->relations as $relation) {
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

            $fragment = $document->createDocumentFragment();
            $fragment->appendXML($template);
            $element->appendChild($fragment);
        }



    }

    private function domSetFormat(\DOMDocument $document, \DOMElement $element)
    {
        $template = <<<XML
<format>$this->format</format>
XML;

        $fragment = $document->createDocumentFragment();
        $fragment->appendXML($template);
        $element->appendChild($fragment);

    }

    private function domSetTitle(\DOMDocument $document, \DOMElement $general, $language = 'nl')
    {
            $template = <<<XML
<title>
    <langstring xml:lang="$language">$this->title</langstring>
</title>
XML;

        $fragment = $document->createDocumentFragment();
        $fragment->appendXML($template);
        $general->appendChild($fragment);

    }

    private function domSetDescription(\DOMDocument $document, \DOMElement $general, $language = 'nl')
    {
        if ($this->description) {

            $template = <<<XML
<description>
    <langstring xml:lang="$language">$this->description</langstring>
</description>
XML;

            $fragment = $document->createDocumentFragment();
            $fragment->appendXML($template);
            $general->appendChild($fragment);
        }

    }

    private static function writeDebug($msg)
    {
        echo $msg . PHP_EOL;
    }

}