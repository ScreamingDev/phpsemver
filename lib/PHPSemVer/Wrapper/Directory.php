<?php

namespace PHPSemVer\Wrapper;


class Directory extends AbstractWrapper
{
    function getAllFileNames()
    {
        return array();
    }

    public function getBasePath()
    {
        return realpath( $this->getBase() ) . DIRECTORY_SEPARATOR;
    }
}