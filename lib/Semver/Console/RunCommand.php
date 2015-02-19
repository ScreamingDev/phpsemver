<?php

namespace Semver\Console;

use PDepend\Source\Language\PHP\PHPBuilder;
use PDepend\Source\Language\PHP\PHPParserGeneric;
use PDepend\Source\Language\PHP\PHPTokenizerInternal;
use PDepend\Util\Cache\CacheFactory;
use Semver\Compare\BuilderCompare;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class RunCommand extends AbstractCommand {
	/**
	 * @param $targets
	 *
	 * @return array
	 */
	protected function _fetchTargets( $targets ) {
		if ( is_dir( $targets ) ) {
			$finder = new Finder();
			$finder->name( '*.php' )
			       ->files()
			       ->in( $targets );

			$targets = array();


			foreach ( $finder as $single_file ) {
				/** @var \Symfony\Component\Finder\SplFileInfo $single_file */

				if ( $single_file->isDir() ) {
					continue;
				}

				$targets[] = $single_file->getPathname();
			}

			return (array) $targets;
		}

		return (array) $targets;
	}

	protected function configure() {
		$this->setName( 'run' );
	}

	protected function execute(
		InputInterface $input, OutputInterface $output
	) {
		$targets = $input->getOption( 'target' );

		$this->debug(
			sprintf( 'Target: %s', $targets )
		);


		$targets = $this->_fetchTargets( $input->getOption( 'target' ) );

		$settings                = new \stdClass();
		$settings->cache         = new \stdClass();
		$settings->cache->driver = 'memory';
		$config                  = new \PDepend\Util\Configuration( $settings );

		$this->cacheFactory = new CacheFactory( $config );

		$this->currentBuilder  = new PHPBuilder();
		$this->previousBuilder = new PHPBuilder();
		$tokenizer             = new PHPTokenizerInternal();

		// fetch current state
		foreach ( $targets as $file ) {

			$tokenizer->setSourceFile( $file );

			$parser = new PHPParserGeneric(
				$tokenizer,
				$this->currentBuilder,
				$this->cacheFactory->create( 'current' )
			);
			$parser->setMaxNestingLevel( 200 );

			try {
				$parser->parse();
			} catch ( \PDepend\Source\Parser\ParserException $e ) {
				$this->parseExceptions[] = $e;
			}
		}

		// fetch previous state
		foreach ( $targets as $file ) {
			$tmp_file = tempnam( sys_get_temp_dir(), 'semver' );

			// last state but suppress error messages
			$oldContent = system(
				"git show HEAD^:" . escapeshellarg( $file ) . ' 2>/dev/null',
				$oldContent
			);

			if ( ! $oldContent ) {
				$oldContent = array();
			}

			file_put_contents( $tmp_file, implode( PHP_EOL, $oldContent ) );

			$tokenizer->setSourceFile( $tmp_file );

			$parser = new PHPParserGeneric(
				$tokenizer,
				$this->previousBuilder,
				$this->cacheFactory->create( 'prev' )
			);
			$parser->setMaxNestingLevel( 200 );

			try {
				$parser->parse();
			} catch ( \PDepend\Source\Parser\ParserException $e ) {
				$this->parseExceptions[] = $e;
			}
		}

		$compare = new BuilderCompare(
			$this->previousBuilder,
			$this->currentBuilder
		);

		$compare->parse();

		$output->writeln('Done!');

	}

	/**
	 * @var PHPBuilder
	 */
	protected $currentBuilder = null;

	/**
	 * @var PHPBuilder
	 */
	protected $previousBuilder = null;
}