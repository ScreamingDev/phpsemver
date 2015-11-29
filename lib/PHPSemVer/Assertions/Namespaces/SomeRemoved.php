<?php

namespace PHPSemVer\Assertions\Namespaces;

use PHPSemVer\Assertions\AbstractAssertion;
use PHPSemVer\Assertions\AssertionInterface;

class SomeRemoved extends AbstractAssertion implements AssertionInterface
{
    public function process()
    {
        $latestNamespaces = array_keys($this->getLatest()->namespaces);

        foreach (array_keys($this->getPrevious()->namespaces) as $namespace) {
            if ( ! in_array($namespace, $latestNamespaces)) {
                $this->appendMessage(
                    sprintf(
                        'Removed namespace "%s".',
                        $namespace
                    )
                );
            }
        }
    }
}