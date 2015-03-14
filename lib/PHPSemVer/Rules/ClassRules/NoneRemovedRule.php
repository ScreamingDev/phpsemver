<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 14.03.15
 * Time: 03:16
 */

namespace PHPSemVer\Rules\ClassRules;


use PHPSemVer\Rules\AbstractRule;
use PHPSemVer\Rules\RuleInterface;

class NoneRemovedRule extends AbstractRule implements RuleInterface
{

    public function process()
    {
        $prevClasses = array();

        foreach ( $this->getLatestBuilder()->getNamespaces() as $namespace )
        {
            foreach ( $namespace->getClasses() as $class )
            {
                $prevClasses[ ] = $namespace->getName() . '\\' . $class->getName();
            }
        }

        foreach ( $this->getPreviousBuilder()->getNamespaces() as $namespace )
        {
            foreach ( $namespace->getClasses() as $class )
            {
                $className = $namespace->getName() . '\\' . $class->getName();
                if ( ! in_array( $className, $prevClasses ) )
                {
                    $this->appendError(
                        sprintf(
                            'Missing class "%s".',
                            $className

                        )
                    );
                }
            }
        }

    }
}