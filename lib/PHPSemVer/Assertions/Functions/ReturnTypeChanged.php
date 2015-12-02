<?php
/**
 * Contains assertion class.
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * https://opensource.org/licenses/MIT. If you did not receive a copy
 * of the PHP License and are unable to obtain it through the web, please send
 * a note to pretzlaw@gmail.com so we can mail you a copy immediately.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */

namespace PHPSemVer\Assertions\Functions;


use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Function_;
use PHPSemVer\Assertions\AbstractAssertion;
use PHPSemVer\Assertions\AssertionInterface;

/**
 * Check if return type changed.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class ReturnTypeChanged extends AbstractAssertion implements AssertionInterface
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
     * Compare functions for their return type.
     *
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