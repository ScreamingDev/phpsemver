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

class SomeAdded extends AbstractAssertion implements AssertionInterface {

	public function process() {
		$prevMethods = array();

		foreach ( $this->getPreviousBuilder()->getNamespaces() as $namespace ) {
			foreach ( $namespace->getClasses() as $class ) {
				foreach ( $class->getAllMethods() as $method ) {
					$prevMethods[] = $namespace->getName()
					                 . '\\' . $class->getName()
					                 . '::' . $method->getName();
				}
			}
		}

		foreach ( $this->getLatestBuilder()->getNamespaces() as $namespace ) {
			foreach ( $namespace->getClasses() as $class ) {
				foreach ( $class->getAllMethods() as $method ) {
					$methodName = $namespace->getName()
					              . '\\' . $class->getName()
					              . '::' . $method->getName();
					if ( ! in_array( $methodName, $prevMethods ) ) {
						$this->appendError(
							sprintf(
								'Added method "%s".',
								$methodName
							)
						);
					}
				}
			}
		}
	}
}