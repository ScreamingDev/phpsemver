<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 14.03.15
 * Time: 03:16
 */

namespace PHPSemVer\Assertions\Functions;


use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Function_;
use PHPSemVer\Assertions\AbstractAssertion;
use PHPSemVer\Assertions\AssertionInterface;

class BodyChanges extends AbstractAssertion implements AssertionInterface
{
    public function process()
    {
        $this->compareList(
            $this->getPrevious()->functions,
            $this->getLatest()->functions
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
     * @param Function_[] $previous
     * @param Function_[] $latest
     * @param string      $namespace
     */
    public function compareList($previous, $latest, $namespace = '')
    {
        $prevFunc = array_keys($previous);

        foreach (array_keys($latest) as $funcName) {
            if ( ! in_array($funcName, $prevFunc)) {
                continue;
            }

            $curFunc    = $latest[$funcName];
            $curReturn  = (string) $curFunc->returnType;
            $prevReturn = (string) $prevFunc[$funcName]->returnType;

            if ($prevReturn != $curReturn) {
                $this->appendMessage(
                    sprintf(
                        'Return type changed "%s\\%s() (%s to %s)".',
                        $namespace,
                        $funcName,
                        $prevReturn,
                        $curReturn
                    )
                );
            }
        }
    }
}