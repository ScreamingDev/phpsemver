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

class SomeAdded extends AbstractAssertion implements AssertionInterface
{
    public function process()
    {
        $this->compareList(
            $this->getPrevious()->functions,
            $this->getLatest()->functions,
            ''
        );

        foreach ($this->getLatest()->namespaces as $namespace => $node) {
            if ( ! isset( $this->getPrevious()->namespaces[$namespace] )) {
                continue;
            }

            $prevNamespace = $this->getPrevious()->namespaces[$namespace];

            $this->compareList(
                $prevNamespace->functions,
                $node->functions,
                $namespace
            );
        }

    }

    /**
     * @param $currentFunctions
     * @param $prevFunc
     * @param $namespace
     */
    public function compareList($prevFunctions, $currentFunctions, $namespace)
    {
        $prevFunc = array_keys($prevFunctions);

        foreach (array_keys($currentFunctions) as $funcName) {
            if ( ! in_array($funcName, $prevFunc)) {
                $this->appendMessage(
                    sprintf(
                        'Added function "%s\\%s()".',
                        $namespace,
                        $funcName
                    )
                );
            }
        }
    }
}