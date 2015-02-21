<?php

namespace PHPSemVer\Wrapper;

use GitWrapper\GitException;
use GitWrapper\GitWrapper;

class Git extends AbstractWrapper
{

    protected $_fileWrapper;

    public function __construct( $base )
    {
        parent::__construct( $base );

        $tmpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid( PHPSEMVER_ID );

        if ( ! is_dir( $tmpPath ) )
        {
            mkdir( $tmpPath, 0777, true );
        }

        $this->_fileWrapper = new Directory( $tmpPath );
    }

    public function getAllFileNames()
    {
        $gitWrapper = new GitWrapper();

        $options = array(
            'with-tree' => $this->getBase(),
        );

        $git = $gitWrapper->workingCopy( getcwd() );

        $result = $git->run(
            array(
                'ls-files',
                $options
            )
        );

        $allPrevious = explode( PHP_EOL, $result->getOutput() );

        return array_filter( $allPrevious );
    }

    public function getPath( $fileName )
    {
        $fullName = $this->getBasePath() . $fileName;

        if ( ! file_exists( $fullName ) )
        {
            $dir = dirname( $fullName );
            if ( ! is_dir( $dir ) )
            {
                mkdir( $dir, 0777, true );
            }

            file_put_contents( $fullName, '' );

            // last state but suppress error messages
            $gitWrapper = new GitWrapper();
            $git        = $gitWrapper->workingCopy( getcwd() );

            try
            {
                $git->run(
                    array(
                        'show',
                        $this->getBase() . '^:' . $fileName
                    )
                );


                $content = $git->getOutput();
            } catch ( GitException $e )
            {
                $content = '';
            }

            file_put_contents( $fullName, $content );
        }

        return $fullName;
    }

    public function getBasePath()
    {
        return $this->_getFileWrapper()->getBasePath();
    }

    /**
     * @return Directory
     */
    protected function _getFileWrapper()
    {
        return $this->_fileWrapper;
    }
}