<?php

namespace PHPSemVer\Rules;


use PDepend\Source\Language\PHP\PHPBuilder;

class AbstractRule
{

    protected $_errors = array();
    protected $_latestBuilder;
    protected $_previousBuilder;

    /**
     * @param PHPBuilder $previousBuilder
     * @param PHPBuilder $latestBuilder
     */
    public function __construct( PHPBuilder $previousBuilder, PHPBuilder $latestBuilder )
    {
        $this->_previousBuilder = $previousBuilder;
        $this->_latestBuilder   = $latestBuilder;
    }

    public function appendError( $message )
    {
        $this->_errors[ ] = new ErrorMessage( get_class( $this ), $message );
    }

    /**
     * @return ErrorMessage[]
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @return PHPBuilder
     */
    public function getLatestBuilder()
    {
        return $this->_latestBuilder;
    }

    /**
     * @return PHPBuilder
     */
    public function getPreviousBuilder()
    {
        return $this->_previousBuilder;
    }


}