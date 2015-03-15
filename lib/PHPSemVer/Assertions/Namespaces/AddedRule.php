<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 14.03.15
 * Time: 03:25
 */

namespace PHPSemVer\Assertions\Namespaces;


use PHPSemVer\Rules\AbstractRule;
use PHPSemVer\Rules\RuleInterface;

class AddedRule extends AbstractRule implements RuleInterface
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
                        'Found new class "%s".',
                        $namespace->getName()
                    )
                );
            }
        }


    }
}