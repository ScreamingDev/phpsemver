<?php

namespace PHPSemVer\Console;

use PDepend\Source\Language\PHP\PHPBuilder;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompareCommand extends AbstractCommand {
	protected $_cacheFactory;
	protected $cacheFactory;
	/**
	 * @var PHPBuilder
	 */
	protected $currentBuilder  = null;
	protected $parseExceptions = array();
	/**
	 * @var PHPBuilder
	 */
	protected $previousBuilder = null;

	protected function configure() {
		$this->setName( 'compare' );

		$this->addOption(
			'type',
			't',
			InputArgument::OPTIONAL,
			'Type of given targets',
			'git'
		);

		$this->addOption(
			'ruleset',
			'R',
			InputArgument::OPTIONAL,
			'A ruleset (eg. "semver2.0")',
			'semver2.0'
		);

		$this->addArgument(
			'previous',
			InputArgument::REQUIRED,
			'Place to lookup the old code'
		);

		$this->addArgument(
			'latest',
			InputArgument::OPTIONAL,
			'Place to lookup the new code',
			'HEAD'
		);
	}

	protected function execute(
		InputInterface $input,
		OutputInterface $output
	) {
		$this->verbose(
			'Comparing %s "%s" with %s "%s" using "%s" ...',
			$input->getOption( 'type' ),
			$input->getArgument( 'latest' ),
			$input->getOption( 'type' ),
			$input->getArgument( 'previous' ),
			$input->getOption( 'ruleset' )
		);

		$wrapper = $this->getWrapperClass( $input->getOption( 'type' ) );

		if ( ! $wrapper ) {
			$output->writeln(
				sprintf(
					'<error>Unknown wrapper-type "%s"</error>',
					$input->getOption( 'type' )
				)
			);

			return;
		}

		$output->writeln( 'Done!' );
	}

	public function getWrapperClass( $name ) {
		$className = '\\PHPSemVer\\Wrapper\\' . ucfirst( $name );

		if ( ! class_exists( $className ) ) {
			return false;
		}

		return $className;
	}
}