<?php

namespace PHPSemVer\Rules;


use DesignPattern\Structural\AbstractComposite;
use PDepend\Source\Language\PHP\PHPBuilder;

class Rule
{
    protected $_fileName;

    function __construct( $targetRule )
    {
        if ( ! file_exists( $targetRule ) )
        {
            throw new \Exception(
                sprintf(
                    'Please provide a valid rule name. ' .
                    'Could not find "%s".',
                    $targetRule
                )
            );
        }

        $this->_fileName = $targetRule;
    }

    /**
     * @param PHPBuilder $previousBuilder
     * @param PHPBuilder $latestBuilder
     */
    public function processAll( PHPBuilder $previousBuilder, PHPBuilder $latestBuilder )
    {
        $errorMessages = array();
        foreach ( $this->getRuleClasses() as $assertionName => $classes )
        {
            if ( ! isset( $errorMessages[ $assertionName ] ) )
            {
                $errorMessages[ $assertionName ] = [ ];
            }

            foreach ( $classes as $className )
            {
                /** @var AbstractRule $singleRule */
                $singleRule = new $className( $previousBuilder, $latestBuilder );
                $singleRule->process();

                $errorMessages[ $assertionName ] = array_merge(
                    $errorMessages[ $assertionName ],
                    $singleRule->getErrors()
                );
            }
        }

        return $errorMessages;
    }

    public function getRuleClasses()
    {
        $ruleClasses = array();

        foreach ( $this->getAllRuleNames() as $ruleSet => $refs )
        {
            if ( ! isset( $ruleClasses[ $ruleSet ] ) )
            {
                $ruleClasses[ $ruleSet ] = [ ];
            }

            foreach ( $refs as $ruleName )
            {
                $class                      = $this->ruleNameToClass( $ruleName );
                $ruleClasses[ $ruleSet ][ ] = $class;
            }
        }

        return $ruleClasses;
    }

    public function getAllRuleNames()
    {
        $xml = simplexml_load_file( $this->_fileName );

        $ruleSet = array();

        foreach ( $xml->xpath( '//ruleset' ) as $singleRuleSet )
        {
            $section = (string) $singleRuleSet->attributes()->name;
            foreach ( $singleRuleSet->xpath( 'rule' ) as $singleRule )
            {
                if ( ! $singleRule->attributes() || ! $singleRule->attributes()->ref )
                {
                    continue;
                }

                $ruleSet[ $section ][ ] = (string) $singleRule->attributes()->ref;
            }
        }

        return $ruleSet;
    }

    public function ruleNameToClass( $ruleName )
    {
        $segments = explode( '.', $ruleName );
        $class    = '\\PHPSemVer\\Rules\\' . implode( '\\', $segments ) . 'Rule';

        if ( ! class_exists( $class ) )
        {
            throw new \Exception(
                sprintf(
                    'Invalid rule "%s" in "%s" (class "%s" not found).',
                    $ruleName,
                    $this->_fileName,
                    $class
                )
            );
        }

        return $class;
    }
}