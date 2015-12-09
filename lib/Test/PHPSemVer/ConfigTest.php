<?php


namespace PHPSemVer;


class ConfigTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \DomainException
     */
    public function testItThrowsExceptionForInvalidScopes()
    {
        $config = $this->makeMinimal();

        $config->getSomethingInvalid();
    }

    /**
     * @return Config
     */
    protected function makeMinimal()
    {
        return new Config('<?xml version="1.0" ?><phpsemver></phpsemver>');
    }

    public function testItIsASimpleXMLElement()
    {
        $config = $this->makeMinimal();

        $this->assertInstanceOf('\SimpleXMLElement', $config);
    }
}
