<?php

namespace PHPSemVer\Compare;

use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Constraint\MajorException;
use PHPSemVer\Constraint\MinorException;
use PHPSemVer\Constraint\NamespaceExists;
use PHPSemVer\Constraint\PatchException;

class BuilderCompare
{
    const OUTPUT_GENERAL = 'general';
    const OUTPUT_MAJOR   = 'major';
    const OUTPUT_MINOR   = 'minor';
    const OUTPUT_PATCH   = 'patch';
    protected $_newNamespaces = array();
    protected $_oldNamespaces = array();
    protected $_output;
    protected $latest;
    protected $previous;

    public function __construct( PHPBuilder $previous, PHPBuilder $latest )
    {
        $this->previous = $previous;

        $this->latest = $latest;
    }

    public function parse()
    {
        $this->testNamespaces();

    }

    public function testNamespaces()
    {
        foreach ( $this->getPrevious()->getNamespaces() as $ast )
        {
            try
            {
                $this->assertNamespaceExists(
                    $ast->getName(),
                    $this->getLatest()
                );
            } catch ( MajorException $e )
            {
                $this->appendException( $e );
            }
        }
    }

    /**
     * @return PHPBuilder
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    public function assertNamespaceExists( $namespace, $ast )
    {
        $assert = new NamespaceExists( $namespace, $ast );

        $assert->run();
    }

    /**
     * @return PHPBuilder
     */
    public function getLatest()
    {
        return $this->latest;
    }

    /**
     * @param \Exception $exception
     *
     * @return null
     */
    public function appendException( $exception )
    {
        if ( $exception instanceof MajorException )
        {
            $this->appendOutput( $exception->getMessage(), static::OUTPUT_MAJOR );

            return null;
        }

        if ( $exception instanceof MinorException )
        {
            $this->appendOutput( $exception->getMessage(), static::OUTPUT_MINOR );

            return null;
        }

        if ( $exception instanceof PatchException )
        {
            $this->appendOutput( $exception->getMessage(), static::OUTPUT_PATCH );

            return null;
        }

        $this->appendOutput( $exception->getMessage() );
    }

    public function appendOutput( $message, $type = self::OUTPUT_GENERAL )
    {
        $this->_output[ $type ][ ] = $message;
    }


}