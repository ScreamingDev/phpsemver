<?php

namespace PHPSemVer;

use PHPSemVer\Rules\AbstractRule;
use PHPSemVer\Rules\RuleSet;

class Specification
{
    protected $_assertions = array();
    protected $_ruleSets = array();

    /**
     * @param AbstractRule $assertionObject Assertion to test against.
     */
    public function addAssertion( AbstractRule $assertionObject )
    {
        $hash = spl_object_hash( $assertionObject );

        $this->_assertions[ $hash ] = $assertionObject;
    }

    public function addRuleSet( $ruleSet )
    {
        $this->_ruleSets[ ] = $ruleSet;
    }

    public function getAssertions()
    {
        return $this->_assertions;
    }

    public function getRuleSets()
    {
        return $this->_ruleSets;
    }

    public function updateFromXmlString( $xmlString )
    {
        $xml = simplexml_load_string( $xmlString );

        foreach ( $xml->xpath( '//ruleSet' ) as $ruleSet )
        {
            $ruleSet = new RuleSet();
        }
    }
}
