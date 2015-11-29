<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 14.03.15
 * Time: 03:16
 */

namespace PHPSemVer\Assertions\Methods;


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

            $prevNamespace = $this->getPrevious()->namespaces[$namespace];

            foreach ($node->classes as $className => $class) {
                if ( ! isset( $prevNamespace->classes[$className] )) {
                    continue;
                }

                $prevClass = $prevNamespace->classes[$className];

                $prevMethods = [];
                foreach ($prevClass->getMethods() as $method) {
                    $prevMethods[] = $method->name;
                }

                foreach ($class->getMethods() as $method) {
                    if ( ! in_array($method->name, $prevMethods)) {
                        $this->appendMessage(
                            sprintf(
                                'Added method "%s\\%s::%s".',
                                $namespace,
                                $className,
                                $method->name
                            )
                        );
                    }
                }
            }
        }

    }
}