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
}
