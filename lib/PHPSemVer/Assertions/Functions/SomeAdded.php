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


use PHPSemVer\Assertions\AbstractAssertion;
use PHPSemVer\Assertions\AssertionInterface;

/**
 * Check if functions are new.
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
     * Compare two lists for new functions.
     *
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