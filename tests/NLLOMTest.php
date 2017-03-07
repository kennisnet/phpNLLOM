<?php

use PHPUnit\Framework\TestCase;

class NLLOMTest extends TestCase
{
    private $loader;

    protected function setUp()
    {
        //$this->loader = new YamlConfigLoader(new FileLocator([__DIR__]));
    }

    public function testCreate()
    {
        $lom = new \Kennisnet\NLLOM();

        $this->assertInstanceOf(\Kennisnet\NLLOM::class, $lom);
    }
}