<?php

namespace PHPSemVer\Wrapper;


use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class Directory extends AbstractWrapper
{
    function getAllFileNames()
    {
        $Directory = new RecursiveDirectoryIterator($this->getBasePath());
        $Iterator  = new RecursiveIteratorIterator($Directory);
        $Regex     = new RegexIterator(
            $Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH
        );

        $allFileNames = array();
        foreach ($Regex as $single) {
            if (false !== strpos($single[0], 'opt')) {
                // todo: add filter rules to xml
                continue;
            }
            $short = str_replace(
                $this->getBasePath(), '', $single[0]
            );
            $allFileNames[$short] = $single[0];
        }

        return $allFileNames;
    }

    public function getBasePath()
    {
        if ( ! $this->getBase()) {
            return '';
        }

        return realpath($this->getBase()) . DIRECTORY_SEPARATOR;
    }

    public function getPath($fileName)
    {
        return $this->getBasePath() . ltrim($fileName, DIRECTORY_SEPARATOR);
    }
}