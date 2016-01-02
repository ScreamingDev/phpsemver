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
			'ruleSet',
			'R',
			InputArgument::OPTIONAL,
			'A predefined rule set or XML file.',
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
			$input->getOption( 'ruleSet' )
		);

		$xmlFile = $this->resolveConfigFile($input->getOption('ruleSet'));

		$this->debug('Using config-file '.$xmlFile);

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

		$config = $this->makeConfig($xmlFile, $output);

		if ($output->isVerbose()) {
			$this->printConfig($config, $output);
		}

		$environment = $this->makeEnvironment($config);

        $this->appendIgnorePattern($xmlFile, $latestWrapper, $previousWrapper);

        $prevTree = $this->parseFiles($previousWrapper, $input->getArgument('previous') . ': ');
        $newTree  = $this->parseFiles($latestWrapper, $input->getArgument('latest') . ': ');

        $output->write('');
        $time = microtime(true);

        $environment->compareTrees($prevTree, $newTree);

        $this->verbose(
            sprintf(
                "\rCompared within %0.2f seconds",
                microtime(true) - $time
            )
        );

        $this->printTable($input, $output, $environment);

        $output->writeln('');
        $this->verbose(
            sprintf(
                'Total time: %.2f',
                microtime(true) - $totalTime
            )
        );
        $this->verbose('');
    }

	/**
	 * Parse files within a wrapper.
	 *
	 * @param AbstractWrapper $wrapper
	 * @param string          $prefix
	 *
	 * @return mixed
	 */
    protected function parseFiles($wrapper, $prefix)
    {
        $this->verbose($prefix . 'Fetching files ...');
        $time       = microtime(true);
        $fileAmount = count($wrapper->getAllFileNames());
        $this->verbose(
            sprintf(
                "\r" . $prefix . "Collected %d files in %0.2f seconds.",
                $fileAmount,
                microtime(true) - $time
            )
        );

        $this->verbose($prefix . 'Parsing ' . $fileAmount . ' files ');
	    $this->debug('  in ' . $wrapper->getBasePath());

        $time     = microtime(true);
        $dataTree = $wrapper->getDataTree();

	    $this->verbose(
		    sprintf(
			    $prefix . "Parsed %d files in %0.2f seconds.",
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
     * @param AbstractWrapper $latestWrapper
     * @param AbstractWrapper $previousWrapper
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

	                foreach ($node->Pattern as $pattern) {
		                $ignorePattern[] = (string) $pattern;
	                }
                }
            }
        }

        $latestWrapper->setExcludePattern($ignorePattern);
        $previousWrapper->setExcludePattern($ignorePattern);
    }

    /**
     * Print information as table.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Environment     $environment
     */
    protected function printTable(InputInterface $input, OutputInterface $output, $environment)
    {
        $table   = new Table($output);
        $headers = array('Level', 'Message');

        if ($input->getOption('print-assertion')) {
            $headers[] = 'Assertion';
        }

        $table->setHeaders($headers);

        foreach ($environment->getConfig()->ruleSet() as $ruleSet) {
            foreach ($ruleSet->getErrorMessages() as $message) {
                $row = array($ruleSet->getName(), $message->getMessage(),);

                if ($input->getOption('print-assertion')) {
                    $row[] = $message->getRule();
                }

                $table->addRow($row);
            }
        }

        $output->writeln('');
        $table->render();
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
			$output->writeln('Using rule set '.$ruleSet->getName());

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
		$environment = new Environment($config);

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
	private function getWrapperInstance($base, $type = 'Directory')
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

	/**
	 * Resolve path to rule set XML.
	 *
	 * @param string $ruleSet Path to XML config file.
	 *
	 * @return string
	 */
	protected function resolveConfigFile($ruleSet)
	{
		if (file_exists($ruleSet)) {
			return $ruleSet;
		}

		$defaultPath = PHPSEMVER_LIB_PATH.'/PHPSemVer/Rules/';
		if (file_exists($defaultPath.$ruleSet)) {
			return $defaultPath.$ruleSet;
		}

		if (file_exists($defaultPath.$ruleSet.'.xml')) {
			return $defaultPath.$ruleSet.'.xml';
		}

		throw new \InvalidArgumentException(
			'Could not find rule set: '.$ruleSet
		);
	}
}