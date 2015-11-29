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
        $prevNamespaces = array_keys($this->getPrevious()->namespaces);

        foreach (array_keys($this->getLatest()->namespaces) as $namespace) {
            if ( ! in_array($namespace, $prevNamespaces)) {
                $this->appendMessage(
                    sprintf(
                        'Added namespace "%s".',
                        $namespace
                    )
                );
            }
        }
    }
}