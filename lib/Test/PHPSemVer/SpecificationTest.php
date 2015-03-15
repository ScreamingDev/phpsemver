<?php

namespace Test\PHPSemVer;


use PHPSemVer\Specification;

class SpecificationTest extends \PHPUnit_Framework_TestCase
{
    public function testXmlCanAddRuleSets()
    {
        $spec = new Specification();

        $xml = $this->getEmptyXml();

        $ruleSet = $xml->addChild( 'ruleSet' );
        $ruleSet->addAttribute( 'name', 'test1' );

        $ruleSet = $xml->addChild( 'ruleSet' );
        $ruleSet->addAttribute( 'name', 'test2' );

        $spec->updateFromXmlString( $xml->asXML() );

        $rules = $spec->getRuleSets();

        $this->assertEquals( 'test1', $rules[ 0 ]->getName() );
        $this->assertEquals( 'test2', $rules[ 1 ]->getName() );
    }

    /**
     * @expectedException \Exception
     */
    public function testXmlThrowsErrorWhenRuleSetNameIsInvalid()
    {
        $spec = new Specification();
        $xml  = $this->getEmptyXml();

        $xml->addChild( 'ruleSet' );

        $spec->updateFromXmlString( $xml->asXML() );
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getEmptyXml()
    {
        return simplexml_load_string( '<?xml version="1.0" ?> <phpsemver></phpsemver>' );
    }
}
