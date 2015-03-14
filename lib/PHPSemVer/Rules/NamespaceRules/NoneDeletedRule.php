<?php

namespace PHPSemVer\Rules\NamespaceRules;

use PHPSemVer\Rules\AbstractRule;
use PHPSemVer\Rules\RuleInterface;

class NoneDeletedRule extends AbstractRule implements RuleInterface
{
    public function process()
    {
        $currentNamespaces = array();

        foreach ( $this->getLatestBuilder()->getNamespaces() as $namespace )
        {
            $currentNamespaces[ ] = $namespace->getName();
        }

        foreach ( $this->getPreviousBuilder()->getNamespaces() as $namespace )
        {
            if ( ! in_array( $namespace->getName(), $currentNamespaces ) )
            {
                $this->appendError(
                    sprintf(
                        'Missing namespace "%s"',
                        $namespace->getName()
                    )
                );
            }
        }
    }
}