<?php

namespace PHPSemVer\Wrapper;


abstract class AbstractWrapper
{
    protected $_base;

    public function __construct( $base )
    {
        $this->_base = $base;
    }

    public function getAllPaths()
    {
        $allPaths = array();

        foreach ( $this->getAllFileNames() as $fileName )
        {
            $allPaths[ $fileName ] = $this->getBasePath() . $fileName;
        }

        return $allPaths;
    }

    abstract public function getAllFileNames();

    abstract public function getBasePath();

    /**
     * @return mixed
     */
    public function getBase()
    {
        return $this->_base;
    }

    public function getPath( $fileName )
    {
        return $this->getPreviousBase() . DIRECTORY_SEPARATOR . $fileName;
    }
}