<?php

namespace Tests\Kennisnet\NLLOM\Library;
use Kennisnet\NLLOM\Library\LomInterval;
use PHPUnit\Framework\TestCase;

class LomIntervalTest extends TestCase
{

    public function testValidDates()
    {
        $lomInterval = new LomInterval('PT10M55S');
        $this->assertEmpty($lomInterval->getHours());
        $this->assertEquals(10, $lomInterval->getMinutes());
        $this->assertEquals(55, $lomInterval->getSeconds());

        $lomInterval = new LomInterval('PT15M46.13S');
        $this->assertEmpty($lomInterval->getHours());
        $this->assertEquals(15, $lomInterval->getMinutes());
        $this->assertEquals(46, $lomInterval->getSeconds());

        $lomInterval = new LomInterval('P6M');
        $this->assertEquals(4320, $lomInterval->getHours());
        $this->assertEmpty($lomInterval->getMinutes());
        $this->assertEmpty($lomInterval->getSeconds());
    }

    public function testInvalidDate()
    {
        $lomInterval = new LomInterval('P');
        $this->assertEmpty($lomInterval->getHours());
        $this->assertEmpty($lomInterval->getMinutes());
        $this->assertEmpty($lomInterval->getSeconds());

        $lomInterval = new LomInterval('');
        $this->assertEmpty($lomInterval->getHours());
        $this->assertEmpty($lomInterval->getMinutes());
        $this->assertEmpty($lomInterval->getSeconds());

        $lomInterval = new LomInterval(null);
        $this->assertEmpty($lomInterval->getHours());
        $this->assertEmpty($lomInterval->getMinutes());
        $this->assertEmpty($lomInterval->getSeconds());
    }

}