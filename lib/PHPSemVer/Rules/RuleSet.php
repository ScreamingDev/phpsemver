<?php

namespace PHPSemVer\Rules;

use PHPSemVer\Assertions\AbstractAssertion;

class RuleSet
{
    protected $_assertions = array();
    protected $_name;

    public function __construct( $name )
    {
        $this->_name = $name;
    }

    public function addAssertion( AbstractAssertion $assertion )
    {
        $hash                       = spl_object_hash( $assertion );
        $this->_assertions[ $hash ] = $assertion;
    }

    public function getAssertions()
    {
        return $this->_assertions;
    }

    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param \SimpleXMLElement $ruleSetXml
     */
    public function updateFromXml( $ruleSetXml )
    {
        foreach ( $ruleSetXml->xpath( 'Assertions' ) as $assertion )
        {
            $namespace = '\\PHPSemVer\\Assertions\\';
            foreach ( $assertion->children() as $section) {
                foreach ($section->children() as $className) {
                    $className = $namespace . $section->getName() . '\\' . $className->getName();

                    if (!class_exists($className)) {
                        throw new \Exception(
                            sprintf(
                                'Please provide valid assertions. '.
                                'Could not find class "%s".',
                                $className
                            )
                        );
                    }
                }
            }
        }
    }
}
