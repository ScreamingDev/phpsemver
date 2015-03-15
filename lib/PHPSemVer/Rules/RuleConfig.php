<?php

namespace PHPSemVer\Rules;

class RuleConfig
{
    protected $_assertions = array();

    public function getAssertions()
    {
        return $this->_assertions;
    }
}
