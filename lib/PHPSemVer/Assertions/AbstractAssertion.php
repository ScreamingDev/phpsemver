<?php

namespace PHPSemVer\Assertions;


use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\DataTree\DataNode;

class AbstractAssertion
{

    protected $_errors = array();
    protected $_latest;
    protected $_previous;

    /**
     * @param PHPBuilder $previous
     * @param PHPBuilder $latest
     */
    public function __construct( DataNode $previous, DataNode $latest )
    {
        $this->_previous = $previous;
        $this->_latest   = $latest;
    }

    public function appendMessage( $message )
    {
        $this->_errors[ ] = new Message( get_class( $this ), $message );
    }

    /**
     * @return Message[]
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @return DataNode
     */
    public function getLatest()
    {
        return $this->_latest;
    }

    /**
     * @return DataNode
     */
    public function getPrevious()
    {
        return $this->_previous;
    }


}