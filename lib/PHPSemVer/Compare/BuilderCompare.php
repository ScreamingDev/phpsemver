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
    protected $latest;
    protected $previous;

    public function __construct( PHPBuilder $previous, PHPBuilder $latest )
    {
        $this->previous = $previous;

        $this->latest = $latest;
    }

    protected $_oldNamespaces = array();
    protected $_newNamespaces = array();

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
                $this->appendException($e);
            }
        }
    }

    const OUTPUT_MAJOR   = 'major';
    const OUTPUT_MINOR   = 'minor';
    const OUTPUT_PATCH   = 'patch';
    const OUTPUT_GENERAL = 'general';

    protected $_output;

    public function appendException( $e )
    {
        if ( $e instanceof MajorException )
        {
            $this->appendOutput( $e->getMessage(), static::OUTPUT_MAJOR );

            return null;
        }

        if ( $e instanceof MinorException )
        {
            $this->appendOutput( $e->getMessage(), static::OUTPUT_MINOR );

            return null;
        }

        if ( $e instanceof PatchException )
        {
            $this->appendOutput( $e->getMessage(), static::OUTPUT_PATCH );

            return null;
        }

        $this->appendOutput( $e->getMessage() );
    }

    public function appendOutput( $message, $type = self::OUTPUT_GENERAL )
    {
        $this->_output[ $type ][ ] = $message;
    }

    public function parse()
    {
        $this->testNamespaces();

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
     * @return PHPBuilder
     */
    public function getPrevious()
    {
        return $this->previous;
    }


}