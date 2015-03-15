<?php

namespace PHPSemVer\Assertions\Namespaces;

use PHPSemVer\Assertions\AbstractAssertion;
use PHPSemVer\Assertions\AssertionInterface;

class NoneDeletedRule extends AbstractAssertion implements AssertionInterface
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