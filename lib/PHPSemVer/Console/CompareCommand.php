<?php
/**
 * Contains console command.
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

namespace PHPSemVer\Console;

use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Config;
use PHPSemVer\Environment;
use PHPSemVer\Wrapper\AbstractWrapper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Compare command.
 *
 * Compare all files based on two versions.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class CompareCommand extends AbstractCommand {
	protected $_cacheFactory;
	protected $cacheFactory;
	/**
	 * Current builder.
	 *
	 * @deprecated 3.0.0
	 *
	 * @var PHPBuilder
	 */
	protected $currentBuilder  = null;
	protected $parseExceptions = array();
	/**
	 * Previous builder
	 *
	 * @deprecated 3.0.0
	 *
	 * @var PHPBuilder
	 */
	protected $previousBuilder = null;

	protected function configure() {
		$this->setName( 'compare' );

		$this->addOption(
			'exclude',
			null,
			InputOption::VALUE_OPTIONAL,
			'Exclude files containing the given string.',
			''
		);

		$this->addOption(
			'type',
			't',
			InputArgument::OPTIONAL,
			'Type of given targets',
			'git'
		);

		$this->addOption(
			'print-assertion',
			null,
			InputOption::VALUE_NONE,
			'Print which assertion caused that warning.'
		);

		$this->addOption(
			'ruleset',
			'R',
			InputArgument::OPTIONAL,
			'A ruleset (eg. "semver2.0")',
			'SemVer2'
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
        $totalTime = microtime(true);

		$this->verbose(
			'Comparing %s "%s" with %s "%s" using "%s" ...',
			$input->getOption( 'type' ),
			$input->getArgument( 'latest' ),
			$input->getOption( 'type' ),
			$input->getArgument( 'previous' ),
			$input->getOption( 'ruleset' )
		);

		$xmlFile = PHPSEMVER_LIB_PATH.'/PHPSemVer/Rules/SemVer2.xml';

		$config      = $this->makeConfig($xmlFile, $output);
		$environment = $this->makeEnvironment($config);

		$this->verbose(
			sprintf(
				'Compare "%s" with "%s"',
				$input->getArgument('previous'),
				$input->getArgument('latest')
			)
		);

		$previousWrapper = $this->getWrapperInstance(
			$input->getArgument('previous'),
			$input->getOption('type')
		);

		$latestWrapper = $this->getWrapperInstance(
			$input->getArgument('latest'),
			$input->getOption('type')
		);

		$previousWrapper->setExcludePattern($input->getOption('exclude'));
		$latestWrapper->setExcludePattern($input->getOption('exclude'));

		$this->appendIgnorePattern($xmlFile, $latestWrapper, $previousWrapper);

		if ($output->isVerbose()) {
			$output->writeln('Fetching previous files ...');
		}

		$previousWrapper->getAllFileNames();

		if ($output->isVerbose()) {
			$output->writeln('Fetchting latest files ...');
		}

		$latestWrapper->getAllFileNames();

		if ($output->isVerbose()) {
			$output->writeln('Comparing ...');
		}

        $prevTree = $this->parseFiles($previousWrapper, $output, $input->getArgument('previous') . ': ');
        $newTree = $this->parseFiles($latestWrapper, $output, $input->getArgument('latest') . ': ');

        $output->write('Comparing ...');
        $time = microtime(true);
		$environment->compareTrees($prevTree,$newTree);
        $output->writeln(
            sprintf(
                "\rComapred within %0.2f seconds",
                microtime(true) - $time
            )
        );

		$table   = new Table( $output );
		$headers = array(
			'Level',
			'Message',
		);

		if ( $input->getOption( 'print-assertion' ) ) {
			$headers[] = 'Assertion';
		}

		$table->setHeaders( $headers );

		foreach ( $environment->getErrorMessages() as $ruleSet => $messages ) {
			if ( ! $messages ) {
				continue;
			}

			foreach ( $messages as $message ) {
				$row = array(
					$ruleSet,
					$message->getMessage(),
				);

				if ( $input->getOption( 'print-assertion' ) ) {
					$row[] = $message->getRule();
				}

				$table->addRow( $row );
			}
		}

        $output->writeln('');
		$table->render();
        $output->writeln('');
        $output->writeln(
            sprintf(
                'Total time: %.2f',
                microtime(true) - $totalTime
            )
        );
        $output->writeln('');
	}

    protected function parseFiles($wrapper, $output, $prefix) {
        $output->write($prefix . 'Collection files ...');
        $time = microtime(true);
        $fileAmount = count($wrapper->getAllFileNames());
        $output->writeln(
            sprintf(
                "\r" . $prefix . "Collected %d files in %0.2f seconds.",
                $fileAmount,
                microtime(true) - $time
            )
        );

        $output->write($prefix . 'Parsing ' . $fileAmount . ' files ...');

        $time = microtime(true);
        $dataTree = $wrapper->getDataTree();

        $output->writeln(
            sprintf(
                "\r" . $prefix . "Parsed %d files in %0.2f seconds.",
                $fileAmount,
                microtime(true) - $time
            )
        );

        return $dataTree;
    }

	public function getWrapperClass( $name ) {
		$className = '\\PHPSemVer\\Wrapper\\' . ucfirst( $name );

		if ( ! class_exists( $className ) ) {
			return false;
		}

		return $className;
	}

    /**
     * Add pattern to exclude files.
     *
     * @param $xmlFile
     * @param $latestWrapper
     * @param $previousWrapper
     */
    protected function appendIgnorePattern(
        $xmlFile, $latestWrapper, $previousWrapper
    ) {
        $config = simplexml_load_file($xmlFile);

        $ignorePattern = [];
        if (isset($config->Filter)) {
            if (isset($config->Filter->Blacklist)) {
                foreach ($config->Filter->Blacklist as $node) {
                    if ( ! isset($node->Pattern)) {
                        continue;
                    }

                    $ignorePattern[] = (string)$node->Pattern;
                }
            }
        }

        $latestWrapper->setExcludePattern($ignorePattern);
        $previousWrapper->setExcludePattern($ignorePattern);
    }

	/**
	 * Print debug information of the used config.
	 *
	 * @param Config          $config
	 * @param OutputInterface $output
	 */
	protected function printConfig(Config $config, OutputInterface $output)
	{
		foreach ($config->ruleSet()->getChildren() as $ruleSet) {
			$output->writeln('Using ruleset '.$ruleSet->getName());

			if ( ! $output->isDebug()) {
				continue;
			}

			if ( ! $ruleSet->trigger()) {
				$output->writeln('  No triggers found.');
				continue;
			}

			foreach ($ruleSet->trigger()->getAll() as $singleTrigger) {
				$output->writeln('  Contains trigger '.$singleTrigger);
			}
		}
	}

	/**
	 * Generate config class from XML-File.
	 *
	 * @param string          $xmlFile
	 * @param OutputInterface $output
	 *
	 * @return Config
	 */
	protected function makeConfig($xmlFile, OutputInterface $output)
	{
		$config = new Config(simplexml_load_file($xmlFile));

		if ($output->isVerbose()) {
			$this->printConfig($config, $output);

			return $config;
		}

		return $config;
	}

	/**
	 * Generate environment from config.
	 *
	 * @param $config
	 *
	 * @return Environment
	 */
	protected function makeEnvironment($config)
	{
		$environment = new Environment();
		$environment->setConfig($config);

		return $environment;
	}

	/**
	 * Create a wrapper for the given target.
	 *
	 * When the target is a directory,
	 * then the type overridden with "Directory".
	 *
	 * @param string $base
	 * @param string $type
	 *
	 * @return AbstractWrapper
	 */
	private function getWrapperInstance($base, $type)
	{
		$wrapper = $this->getWrapperClass($type);

		if ( ! $wrapper) {
			throw new \InvalidArgumentException(
				sprintf(
					'<error>Unknown wrapper-type "%s"</error>',
					$type
				)
			);
		}

		if (is_dir($base)) {
			$wrapper = $this->getWrapperClass('Directory');
		}

		$this->debug('Using wrapper "'.$wrapper.'" for "'.$base.'"');

		return new $wrapper($base);
	}
}