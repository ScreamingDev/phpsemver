<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 14.03.15
 * Time: 03:25
 */

namespace PHPSemVer\Assertions\Classes;


use PHPSemVer\Assertions\AbstractAssertion;
use PHPSemVer\Assertions\AssertionInterface;

class SomeAdded extends AbstractAssertion implements AssertionInterface
{

    public function process()
    {
        foreach ($this->getLatest()->namespaces as $namespace => $node) {
            if ( ! isset( $this->getPrevious()->namespaces[$namespace] )) {
                continue;
            }

            $prevClasses = array_keys(
                $this->getPrevious()->namespaces[$namespace]->classes
            );

            foreach (array_keys($node->classes) as $className) {
                if ( ! in_array($className, $prevClasses)) {
                    $this->appendMessage(
                        sprintf(
                            'Added class "%s\\%s".',
                            $namespace,
                            $className
                        )
                    );
                }
            }
        }

    }
}