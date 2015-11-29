<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 14.03.15
 * Time: 03:16
 */

namespace PHPSemVer\Assertions\Methods;


use PHPSemVer\Assertions\AbstractAssertion;
use PHPSemVer\Assertions\AssertionInterface;

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