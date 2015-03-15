<?php

namespace Test\PHPSemVer;


use PHPSemVer\Specification;

class SpecificationTest extends \PHPUnit_Framework_TestCase
{
    public function testItAddsConfigOfXmlFiles()
    {
        $xml = PHPSEMVER_LIB_PATH
               . DIRECTORY_SEPARATOR . 'PHPSemVer'
               . DIRECTORY_SEPARATOR . 'Rules'
               . DIRECTORY_SEPARATOR . 'SemVer2.xml';

        $spec = new Specification();

        $spec->updateFromXmlString( file_get_contents( $xml ) );

        $ruleSets = $spec->getRuleSets();

        $this->assertCount( 3, $spec->getRuleSets() );
        $this->assertEquals( 'major', $ruleSets[ 0 ]->getName() );
        $this->assertEquals( 'patch', $ruleSets[ 2 ]->getName() );
    }

    public function testXmlCanAddRuleSets()
    {
        $spec = new Specification();

        $xml = $this->getEmptyXml();

        $ruleSet = $xml->addChild( 'RuleSet' );
        $ruleSet->addAttribute( 'name', 'test1' );

        $ruleSet = $xml->addChild( 'RuleSet' );
        $ruleSet->addAttribute( 'name', 'test2' );

        $spec->updateFromXmlString( $xml->asXML() );

        $rules = $spec->getRuleSets();

        $this->assertEquals( 'test1', $rules[ 0 ]->getName() );
        $this->assertEquals( 'test2', $rules[ 1 ]->getName() );
    }

    /**
     * @return \SimpleXMLElement
     */
    public function getEmptyXml()
    {
        return simplexml_load_string( '<?xml version="1.0" ?> <phpsemver></phpsemver>' );
    }

    /**
     * @expectedException \Exception
     */
    public function testXmlThrowsErrorWhenRuleSetNameIsInvalid()
    {
        $spec = new Specification();
        $xml  = $this->getEmptyXml();

        $xml->addChild( 'RuleSet' );

        $spec->updateFromXmlString( $xml->asXML() );
    }
}
