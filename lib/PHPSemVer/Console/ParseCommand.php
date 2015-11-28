<?php

namespace PHPSemVer\Console;

use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Parser\PHP\FileParser;
use PHPSemVer\Rules\Rule;
use PHPSemVer\Rules\RuleCollection;
use PHPSemVer\Rules\RuleFactory;
use PHPSemVer\Wrapper\Directory;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ParseCommand extends AbstractCommand {
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
		$this->setName( 'parse' );
	}

	protected function execute(
		InputInterface $input,
		OutputInterface $output
	) {
		$parser = new FileParser(__FILE__);

		var_dump($parser->getAST());

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