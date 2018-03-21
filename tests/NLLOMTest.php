<?php

namespace Tests\Kennisnet\NLLOM;

use Kennisnet\NLLOM\DomToLomMapper;
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
use Kennisnet\NLLOM\LomToDomMapper;
use Kennisnet\NLLOM\NLLOM;
use Kennisnet\NLLOM\Validator;
use PHPUnit\Framework\TestCase;

class NLLOMTest extends TestCase
{
    /**
     * Build and parse a complete NLLOM structure, including all optional/recommended/business fields.
     *
     * Also checks if default language (nl) is set on every component
     *
     * @throws \Exception
     */
    public function testCompleteCreate()
    {
        $lom = new \Kennisnet\NLLOM\NLLOM();

        $lom
            ->setGeneralTitle(new LomMultiLanguage([
                new LomLanguageString('Test <strike>test</strike>'),
                new LomLanguageString('Eine kleine nachtmusik', 'de')
            ]))
            ->addGeneralDescription(new LomMultiLanguage([
                new LomLanguageString('één beschrijving met speciale tekens')
            ]))
            ->addGeneralIdentifier(new LomIdentifier(
                    'uri',
                    'urn:isbn:9789034553966')
            )
            ->addGeneralIdentifier(new LomIdentifier(
                    'uri',
                    'https://delen.edurep.nl/download.php?id=1c1aad84-a96b-4efe-8d23-25c870566497&test=1')
            )
            ->addGeneralLanguage('nl')
            ->addGeneralLanguage('en')
            ->addGeneralKeyword(new LomMultiLanguage([
                new LomLanguageString('Nederlands'),
                new LomLanguageString('Dutch', 'en')
            ]))
            ->addGeneralKeyword(new LomMultiLanguage([
                new LomLanguageString('Engels')
            ]))
            ->setGeneralAggregationLevel(new LomTerm(2))
            ->addGeneralCoverage(new LomMultiLanguage([
                    new LomLanguageString('Frankrijk in de 16de eeuw')]
            ))
            ->setGeneralStructure(new LomTerm('networked'))
            ->setLifecycleVersion(new LomString('07122005 124436'))
            ->setLifecycleStatus(new LomTerm('final'));

        $vcard = <<<VCARD
BEGIN:VCARD
VERSION:3.0
N:Digischool
FN:Digischool
ORG:Digischool
EMAIL;TYPE=INTERNET,PREF:redactie@digischool.nl
URL:http://www.digischool.nl/
END:VCARD
VCARD;

        $lom
            ->addLifecycleContributor(
                new LomContribute(
                    new LomTerm('author'),
                    [
                        new LomString($vcard)
                    ],
                    new LomDateTime(
                        new LomString('1999-06-01'),
                        new LomMultiLanguage([
                            new LomLanguageString('Omschrijving')
                        ])
                    )
                )
            )
            ->addLifecycleContributor(
                new LomContribute(
                    new LomTerm('publisher'),
                    [],
                    new LomDateTime(
                        new LomString('1999-06-01')
                    )
                )
            )
            ->addMetaMetadataContributor(new LomContribute(
                new LomTerm('creator'),
                [],
                new LomDateTime(new LomString('1999-06-01'))
            ))
            ->addMetaMetadataContributor(new LomContribute(
                new LomTerm('validator'),
                [
                    new LomString($vcard)
                ],
                new LomDateTime(new LomString('1999-06-01'))
            ))
            ->addMetaMetadataIdentifier(new LomIdentifier('uri', 'urn:isbn:9789034553966'))
            ->setMetaMetadataLanguage(new LomString('nl'))
            ->addMetaMetadataSchema(new LomString('LOMv1.0'))
            ->addMetaMetadataSchema(new LomString('nl_lom_v1p0'))
            ->addTechnicalFormat(new LomString('application/pdf'))
            ->setTechnicalSize(new LomString(555666))
            ->addTechnicalLocation(
                new LomString('https://delen.edurep.nl/download.php?id=1c1aad84-a96b-4efe-8d23-25c870566497&test=1')
            )
            ->setTechnicalRemarks(new LomMultiLanguage([
                new LomLanguageString('Pak het zipbestand uit en start index.html in je webbrowser')
            ]))
            ->setTechnicalDuration(
                new LomDuration(
                    new LomInterval('PT1H30M'),
                    new LomMultiLanguage([
                        new LomLanguageString('Tijd inc aftiteling')
                    ])
                )
            );

        $lom
            ->setEducationalInteractivityType(new LomTerm('mixed'))
            ->addEducationalDescription(new LomMultiLanguage([
                new LomLanguageString('Doe deze opdracht in groepjes van 3 tot 5 personen.')
            ]))
            ->addEducationalLearningResourceType(
                new LomTerm(
                    'open opdracht',
                    'http://purl.edustandaard.nl/vdex_learningresourcetype_czp_20060628.xml'
                )
            )
            ->setEducationalInteractivityLevel(new LomTerm('very high'))
            ->setEducationalSemanticDensity(new LomTerm('very low'))
            ->addEducationalIntendedUserRole(new LomTerm('learner'))
            ->addEducationalContext(
                new LomTerm(
                    'VO',
                    'http://purl.edustandaard.nl/vdex_context_czp_20060628.xml'
                )
            )
            ->addEducationalTypicalAgeRange(new LomString('8-13'))
            ->setEducationalDifficulty(
                new LomTerm(
                    'very easy',
                    'http://purl.edustandaard.nl/vdex_difficulty_lomv1p0_20060628.xml'
                )
            )
            ->setEducationalTypicalLearningTime(
                new LomDuration(
                    new LomInterval('PT1H30M'),
                    new LomMultiLanguage([
                        new LomLanguageString('De daadwerkelijke tijd, niet de doorlooptijd.')
                    ])
                )
            )
            ->addEducationalLanguage(new LomString('nl'));

        $lom
            ->setRightsCost(new LomTerm('no'))
            ->setRightsCopyright(
                new LomTerm(
                    'cc-by-30',
                    'http://purl.edustandaard.nl/copyrightsandotherrestrictions_nllom_20110411'
                )
            )
            ->setRightsDescription(new LomMultiLanguage([
                new LomLanguageString('Anderen mogen het werk gebruiken')
            ]));

        $lom
            ->addRelation(new LomRelation(
                new LomTerm('embed', 'http://purl.edustandaard.nl/relation_kind_nllom_20131211'),
                [
                    new LomIdentifier('uri', 'http://www.vimdeo.com/dummy1')
                ],
                new LomMultiLanguage([
                    new LomLanguageString('Test 1')
                ])
            ))
            ->addRelation(new LomRelation(
                null,
                [
                    new LomIdentifier('uri', 'http://www.vimdeo.com/dummy2')
                ]
            ));

        $lom->addClassification(
            new LomClassification(
                new LomTerm(
                    'discipline',
                    'http://download.edustandaard.nl/vdex/vdex_classification_purpose_czp_20060628.xml'
                ),
                [
                    new LomTaxonPath(
                        new LomString(
                            'http://download.edustandaard.nl/vdex/vdex_classification_vakaanduidingen_vo_20071115.xml'
                        ),
                        [
                            new LomTaxon(
                                new LomString('Duits'),
                                new LomMultiLanguage([
                                    new LomLanguageString('Duitse taal')
                                ])
                            )
                        ]
                    )
                ]
            )
        );

        $testLom = clone $lom;

        // Check if duplicate sources are merged into single source in result.xml
        $lom->addClassification(
            new LomClassification(
                new LomTerm(
                    'educational level'
                ),
                [
                    new LomTaxonPath(
                        new LomString(
                            'http://download.edustandaard.nl/vdex/vdex_classification_educationallevel_czp_20071115.xml'
                        ),
                        [
                            new LomTaxon(
                                new LomString('VO'),
                                new LomMultiLanguage([
                                    new LomLanguageString('Voortgezet Onderwijs', 'nl')
                                ])
                            ),
                            new LomTaxon(
                                new LomString('vmbo'),
                                new LomMultiLanguage([
                                    new LomLanguageString('VMBO', 'nl')
                                ])
                            )
                        ]
                    ),
                    new LomTaxonPath(
                        new LomString(
                            'http://download.edustandaard.nl/vdex/vdex_classification_educationallevel_czp_20071115.xml'
                        ),
                        [
                            new LomTaxon(
                                new LomString('VWO'),
                                new LomMultiLanguage([
                                    new LomLanguageString('VWO', 'nl')
                                ])
                            )
                        ]
                    ),
                    new LomTaxonPath(
                        new LomString(
                            'http://download.edustandaard.nl/vdex/vdex_classification_educationallevel_czp_20071115.xml'
                        ),
                        [
                            new LomTaxon(
                                new LomString('Duits'),
                                new LomMultiLanguage([
                                    new LomLanguageString('Duits', 'nl')
                                ])
                            ),
                            new LomTaxon(
                                new LomString('vmbo_kl2'),
                                new LomMultiLanguage([
                                    new LomLanguageString('VMBO kaderberoepsgerichte leerweg, 2')
                                ])
                            )
                        ]
                    )
                ]
            )
        );

        // Validate generated Dom/XML against result.xml
        $mapper = new LomToDomMapper();
        $dom = $mapper->lomToDom($lom);

        $result = $dom->saveXML();

        $this->assertEquals($this->getExpectedXML('result.xml'), $result);

        Validator::validate($result);

        //Validate parsing XML to NLLOM object
        $domDocument = new \DOMDocument('1.0', 'utf-8');
        $domDocument->load(__DIR__ . '/result.xml');

        $mapper = new DomToLomMapper();
        $nllom = $mapper->domToLom($domDocument);

        // Classifications are merged in xml, so use cloned object to reflect that
        $testLom->addClassification(
            new LomClassification(
                new LomTerm(
                    'educational level'
                ),
                [
                    new LomTaxonPath(
                        new LomString(
                            'http://download.edustandaard.nl/vdex/vdex_classification_educationallevel_czp_20071115.xml'
                        ),
                        [
                            new LomTaxon(
                                new LomString('VO'),
                                new LomMultiLanguage([
                                    new LomLanguageString('Voortgezet Onderwijs')
                                ])
                            ),
                            new LomTaxon(
                                new LomString('vmbo'),
                                new LomMultiLanguage([
                                    new LomLanguageString('VMBO')
                                ])
                            ),
                            new LomTaxon(
                                new LomString('VWO'),
                                new LomMultiLanguage([
                                    new LomLanguageString('VWO')
                                ])
                            ),
                            new LomTaxon(
                                new LomString('Duits'),
                                new LomMultiLanguage([
                                    new LomLanguageString('Duits')
                                ])
                            ),
                            new LomTaxon(
                                new LomString('vmbo_kl2'),
                                new LomMultiLanguage([
                                    new LomLanguageString('VMBO kaderberoepsgerichte leerweg, 2')
                                ])
                            )
                        ]
                    ),
                ]
            )
        );

        $this->assertEquals($nllom, $testLom);
    }


    /**
     * Check if all manual language overrides work
     *
     * @throws \Exception
     */
//    public function testLanguage()
//    {
//        $lom = new \Kennisnet\NLLOM\NLLOM([
//            'language' => 'en'
//        ]);
//
//        $lom->setGeneralTitle(new LomLanguageString('Test <strike>test</strike>', 'en'))
//
//            ->addGeneralDescription([
//                new LomLanguageString('één beschrijving met speciale tekens', 'en'),
//                new LomLanguageString('één beschrijving met speciale tekens', 'de')
//            ])
//            ->addGeneralDescription([
//                new LomLanguageString('tweede beschrijving zonder speciale tekens', 'de')
//            ])
//
//
//            ->addGeneralKeyword(new LomLanguageString('Nederlands', 'nl'))
//            ->addGeneralKeyword(new LomLanguageString('Engels', 'de'))
//            ->addGeneralCoverage(new LomLanguageString('Frankrijk in de 16de eeuw', 'nl'))
//            ->addGeneralCoverage(new LomLanguageString('Frankrijk in de 16de eeuw', 'de'));
//
//        $lom
//            ->addLifecycleContributor(
//                new LomContribute(
//                    new LomTerm('author'),
//                    [
//                    ],
//                    new LomDateTime(
//                        new LomString('1999-06-01'),
//                        new LomLanguageString('Omschrijving', 'nl')
//                    )
//                )
//            )
//            ->addMetaMetadataContributor(new LomContribute(
//                new LomTerm('creator'),
//                [],
//                new LomDateTime(new LomString('1999-06-01'), new LomLanguageString('Omschrijving', 'nl'))
//            ))
//            ->setTechnicalRemarks(
//                new LomLanguageString('Pak het zipbestand uit en start index.html in je webbrowser', 'nl')
//            )
//            ->setTechnicalDuration(
//                new LomDuration(
//                    new LomInterval('PT1H30M'),
//                    new LomLanguageString('Tijd inc aftiteling', 'nl')
//                )
//            );
//
//        $lom
//            ->addEducationalDescription(new LomLanguageString(
//                    'Doe deze opdracht in groepjes van 3 tot 5 personen.',
//                    'nl'
//                )
//            )
//            ->setEducationalTypicalLearningTime(
//                new LomDuration(
//                    new LomInterval('PT1H30M'),
//                    new LomLanguageString('De daadwerkelijke tijd, niet de doorlooptijd.', 'nl')
//                )
//            )
//            ->setRightsDescription(new LomLanguageString('Anderen mogen het werk gebruiken', 'nl'))
//            ->addRelation(new LomRelation(
//                new LomTerm('embed', 'http://purl.edustandaard.nl/relation_kind_nllom_20131211'),
//                [
//                    new LomIdentifier('uri', 'http://www.vimdeo.com/dummy1')
//                ],
//                new LomLanguageString('Test 1', 'nl')
//            ))
//            ->addClassification(
//                new LomClassification(
//                    new LomTerm(
//                        'discipline',
//                        'http://download.edustandaard.nl/vdex/vdex_classification_purpose_czp_20060628.xml'
//                    ),
//                    [
//                        new LomTaxonPath(
//                            new LomString(
//                                'http://download.edustandaard.nl/vdex/vdex_classification_vakaanduidingen_vo_20071115.xml'
//                            ),
//                            [
//                                new LomTaxon(
//                                    new LomString('Duits'),
//                                    new LomLanguageString('Duitse taal', 'nl')
//                                )
//                            ]
//                        )
//                    ]
//                )
//            );
//
//        // Validate generated Dom/XML against result.xml
//        $mapper = new LomToDomMapper();
//        $dom = $mapper->lomToDom($lom);
//
//        $result = $dom->saveXML();
//
//        $this->assertEquals($this->getExpectedXML('language.xml'), $result);
//
//        Validator::validate($result);
//
//        //Validate parsing XML to NLLOM object
//        $domDocument = new \DOMDocument('1.0', 'utf-8');
//        $domDocument->load(__DIR__ . '/language.xml');
//
//        $mapper = new DomToLomMapper();
//        $nllom = $mapper->domToLom($domDocument, [
//            'language' => 'en'
//        ]);
//
//        $this->assertEquals($nllom, $lom);
//    }

    /**
     * Build and parse a minimal NLLOM structure, required fields only
     *
     * @throws \Exception
     */
    public function testMinimalCreate()
    {
        $lom = new NLLOM();

        $mapper = new LomToDomMapper();
        $dom = $mapper->lomToDom($lom);

        $result = $dom->saveXML();

        $this->assertEquals($this->getExpectedXML('result_minimal.xml'), $result);
        Validator::validate($result);

        //Test parsing XML to object
        $domDocument = new \DOMDocument('1.0', 'utf-8');
        $domDocument->load(__DIR__ . '/result_minimal.xml');

        $mapper = new DomToLomMapper();
        $nllom = $mapper->domToLom($domDocument);

        $this->assertEquals($nllom, $lom);
    }

    public function testInvalidValue()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid value');
        $lom = new NLLOM();
        $lom->setTechnicalSize(new LomString('bla'));
    }

    public function testDuplicateCatalog()
    {
        $lom = new NLLOM();
        $lom->addGeneralIdentifier(new LomIdentifier('cat', 'entry'));
        $lom->addGeneralIdentifier(new LomIdentifier('cat', 'entry'));
        $lom->addGeneralIdentifier(new LomIdentifier('cat2', 'entry2'));

        $this->assertEquals(2, count($lom->getGeneralIdentifiers()));
    }

    /**
     * Format expected result in same way as results
     * @param $filename
     * @return string
     */
    private function getExpectedXML($filename)
    {
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $dom->load(__DIR__ . '/' . $filename);
        return $dom->saveXML();
    }
}
