<?php
/**
 * Contains assertion.
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

namespace PHPSemVer\Assertions\Methods;


use PHPSemVer\Assertions\AbstractAssertion;
use PHPSemVer\Assertions\AssertionInterface;

/**
 * Check if methods were removed.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class SomeRemoved extends AbstractAssertion implements AssertionInterface {

	public function process()
	{
		foreach ($this->getPrevious()->namespaces as $namespace => $node) {
			if ( ! isset( $this->getLatest()->namespaces[$namespace] )) {
				continue;
			}

			$latestNamespace = $this->getLatest()->namespaces[$namespace];

			foreach ($node->classes as $className => $class) {
				if ( ! isset( $latestNamespace->classes[$className] )) {
					continue;
				}

				$latestClass = $latestNamespace->classes[$className];

				$latestMethods = [];
				foreach ($latestClass->getMethods() as $method) {
					$latestMethods[] = $method->name;
				}

				foreach ($class->getMethods() as $method) {
					if ( ! in_array($method->name, $latestMethods)) {
						$this->appendMessage(
							sprintf(
								'Removed method "%s\\%s::%s".',
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