<?php
/**
 * Assertion class.
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
use PHPSemVer\DataTree\DataNode;

/**
 * Check if some functions are removed.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
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
     * Check two statement lists for new functions.
     *
     * @param DataNode $prevFunctions
     * @param DataNode $currentFunctions
     * @param string   $namespace
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