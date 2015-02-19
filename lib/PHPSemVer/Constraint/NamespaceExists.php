<?php

namespace PHPSemVer\Constraint;

class NamespaceExists extends AbstractAssertion {
	public function __construct( $namespace, $astSet ) {
		$this->setNamespace( $namespace );
		$this->setAstSet( $astSet );
	}

	protected $_astSet    = array();
	protected $_namespace = '';

	/**
	 * @return array
	 */
	public function getAstSet() {
		return $this->_astSet;
	}

	/**
	 * @param array $astSet
	 */
	public function setAstSet( $astSet ) {
		$this->_astSet = $astSet;
	}

	/**
	 * @return string
	 */
	public function getNamespace() {
		return $this->_namespace;
	}

	/**
	 * @param string $namespace
	 */
	public function setNamespace( $namespace ) {
		$this->_namespace = $namespace;
	}

	public function run() {
		foreach ( $this->getAstSet() as $ast ) {
			if ( $ast->getName() == $this->getNamespace() ) {
				return true;
			}
		}

		throw new MajorException(
			$this->__(
				'Namespace "%s" is missing or renamed.',
				$this->getNamespace()
			)
		);
	}
}