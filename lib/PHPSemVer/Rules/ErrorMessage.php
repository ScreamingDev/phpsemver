<?php

namespace PHPSemVer\Rules;


class ErrorMessage
{
    protected $_message;
    protected $_rule;

    /**
     * @param string $rule
     * @param string $message
     */
    public function __construct( $rule, $message )
    {
        $this->_rule    = $rule;
        $this->_message = $message;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @return mixed
     */
    public function getRule()
    {
        return $this->_rule;
    }


}