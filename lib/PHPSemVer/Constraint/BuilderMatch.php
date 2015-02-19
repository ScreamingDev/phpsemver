<?php

namespace PHPSemVer\Constraint;


class BuilderMatch extends AbstractAssertion {

	protected $_latest;
	protected $_previous;

	/**
	 * @return mixed
	 */
	public function getLatest() {
		return $this->_latest;
	}

	/**
	 * @param mixed $latest
	 */
	public function setLatest( $latest ) {
		$this->_latest = $latest;
	}

	/**
	 * @return mixed
	 */
	public function getPrevious() {
		return $this->_previous;
	}

	/**
	 * @param mixed $previous
	 */
	public function setPrevious( $previous ) {
		$this->_previous = $previous;
	}



	function __construct( $latest, $previous ) {
		$this->setLatest($latest);
		$this->setPrevious($previous);
	}


	function run() {

	}
}