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

namespace PHPSemVer\Assertions\Classes;


use PHPSemVer\Assertions\AbstractAssertion;
use PHPSemVer\Assertions\AssertionInterface;

/**
 * Check if some classes were removed.
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
        foreach ($this->getPrevious()->namespaces as $namespace => $node) {
            if ( ! isset( $this->getLatest()->namespaces[$namespace] )) {
                continue;
            }

            $prevClasses = array_keys(
                $this->getLatest()->namespaces[$namespace]->classes
            );

            foreach (array_keys($node->classes) as $className) {
                if ( ! in_array($className, $prevClasses)) {
                    $this->appendMessage(
                        sprintf(
                            'Removed class "%s\\%s".',
                            $namespace,
                            $className
                        )
                    );
                }
            }
        }
    }
}