<?php


namespace PHPSemVer;


class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testGetterReturnNullWhenAttributeNotExists()
    {
        $config = $this->makeFull();

        $this->assertNull($config->getSomeFooBarBaz());
    }

    /**
     * @expectedException \DomainException
     */
    public function testItThrowsExceptionForInvalidScopes()
    {
        $config = $this->makeFull();

        $config->somethingInvalid();
    }

    protected function makeFull()
    {
        return new Config(simplexml_load_file(__DIR__ . '/Config/full.xml'));
    }

    public function testItIsASimpleXMLElementFacade()
    {
        $config = $this->makeFull();

        $this->assertInstanceOf('\SimpleXMLElement', $config->getXml());
    }

    public function testItHasAttributes()
    {
        $config = $this->makeFull();

        $this->assertNotEmpty($config->getTitle());
    }
}
