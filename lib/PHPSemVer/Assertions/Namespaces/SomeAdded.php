<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 14.03.15
 * Time: 03:25
 */

namespace PHPSemVer\Assertions\Namespaces;


use PHPSemVer\Assertions\AbstractAssertion;
use PHPSemVer\Assertions\AssertionInterface;

class SomeAdded extends AbstractAssertion implements AssertionInterface
{

    public function process()
    {
        $prevNamespaces = array();
        foreach ( $this->getPreviousBuilder()->getNamespaces() as $namespace )
        {
            $prevNamespaces[ ] = $namespace->getName();
        }

        foreach ( $this->getLatestBuilder()->getNamespaces() as $namespace )
        {
            if ( ! in_array( $namespace->getName(), $prevNamespaces ) )
            {
                $this->appendError(
                    sprintf(
                        'Added namespace "%s".',
                        $namespace->getName()
                    )
                );
            }
        }


    }
}