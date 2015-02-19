<?php

namespace PHPSemVer\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractCommand
 *
 * @package Softec\Console
 *
 */
abstract class AbstractCommand extends Command {
	protected $_input;
	protected $_output;

	public function getPrdPath() {
		return $this->getInput()->getOption( 'prd-path' );
	}

	/**
	 * @return \PHPSemVer\Console\Application
	 */
	public function getApplication() {
		return parent::getApplication();
	}


	/**
	 * @return InputInterface
	 */
	public function getInput() {
		return $this->_input;
	}

	/**
	 * @param InputInterface $input
	 */
	public function setInput( $input ) {
		$this->_input = $input;
	}

	/**
	 * @return OutputInterface
	 */
	public function getOutput() {
		return $this->_output;
	}

	/**
	 * @param OutputInterface $output
	 */
	public function setOutput( $output ) {
		$this->_output = $output;
	}

	protected function initialize(
		InputInterface $input,
		OutputInterface $output
	) {
		$this->setInput( $input );
		$this->setOutput( $output );

		parent::initialize(
			$input,
			$output
		);
	}

	protected $_outputDocument;

	public function debug( $message ) {
		if ( ! $this->getOutput()->isDebug() ) {
			return null;
		}

		$this->getOutput()->writeln( $message );
	}

	public function verbose( $message ) {
		if ( ! $this->getOutput()->isVerbose() ) {
			return null;
		}

		$this->getOutput()->writeln( '<info>' . $message . '</info>' );
	}
}