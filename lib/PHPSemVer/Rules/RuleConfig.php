<?php

namespace PHPSemVer\Rules;

class RuleConfig
{
    protected $_assertions = array();

    /**
     * @param AbstractRule $assertionObject Assertion to test against.
     */
    public function addAssertion( AbstractRule $assertionObject )
    {
        $hash = spl_object_hash( $assertionObject );

        $this->_assertions[ $hash ] = $assertionObject;
    }

    public function getAssertions()
    {
        return $this->_assertions;
    }
}
