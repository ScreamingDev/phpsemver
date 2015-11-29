<?php

namespace PHPSemVer\Console;

use PDepend\Source\Language\PHP\PHPBuilder;
use PhpParser\Lexer\Emulative;
use PhpParser\Parser;
use PHPSemVer\DataTree\DataNode;
use PHPSemVer\DataTree\Importer\NikicParser;
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

function foo_me() {
	echo 'foo_you';
}

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
		ini_set('xdebug.max_nesting_level', 3000);

		$parser = new Parser(new Emulative);

		$code = file_get_contents(__FILE__);

		$translator = new NikicParser();
		$dataTree   = new DataNode();
		$translator->importStmts($parser->parse($code), $dataTree);

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