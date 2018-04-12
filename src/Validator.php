<?php
namespace Kennisnet\NLLOM;

// Enable local loading of XSD files for validation
$mapping = [
    'http://www.imsglobal.org/xsd/imsmd_v1p2p4.xsd' => 'imsmd_v1p2p4.xsd',
    'http://www.w3.org/2001/xml.xsd' => 'xml.xsd',
    'https://www.w3.org/2001/03/xml.xsd' => 'xml_3.xsd',
    'http://www.w3.org/2001/03/xml.xsd' => 'xml_3.xsd',
];

libxml_set_external_entity_loader(
    function ($public, $system, $context) use ($mapping) {

        if (is_file($system)) return $system;

        $path = realpath(__DIR__ . '/xsd/' . basename($system));

        if (!is_file($path)) {
            throw new \Exception($system . ' not found');
        }

        return $path;
    }
);

class Validator
{

    /**
     * @param $xml
     * @return bool
     */
    public static function validate($xml)
    {
        $domDocumentValidate = new \DOMDocument('1.0', 'UTF-8');
        $domDocumentValidate->loadXML($xml);

        return $domDocumentValidate->schemaValidate('xsd/imsmd_v1p2p4.xsd');
    }
}
