<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 14.03.15
 * Time: 03:16
 */

namespace PHPSemVer\Assertions\Functions;


use PHPSemVer\Assertions\AbstractAssertion;
use PHPSemVer\Assertions\AssertionInterface;

class SomeRemoved extends AbstractAssertion implements AssertionInterface
{
    public function process()
    {
        $this->compareList(
            $this->getPrevious()->functions,
            $this->getLatest()->functions
        );

        foreach ($this->getPrevious()->namespaces as $namespace => $node) {
            if ( ! isset( $this->getLatest()->namespaces[$namespace] )) {
                continue;
            }

            $latestNs = $this->getLatest()->namespaces[$namespace];

            $this->compareList(
                $node->functions,
                $latestNs->functions,
                $namespace
            );
        }

    }

    /**
     * @param $currentFunctions
     * @param $prevFunc
     * @param $namespace
     */
    public function compareList(
        $prevFunctions,
        $currentFunctions,
        $namespace = ''
    ) {
        $lastFunc = array_keys($currentFunctions);

        foreach (array_keys($prevFunctions) as $funcName) {
            if ( ! in_array($funcName, $lastFunc)) {
                $this->appendMessage(
                    sprintf(
                        'Removed function "%s\\%s()".',
                        $namespace,
                        $funcName
                    )
                );
            }
        }
    }
}