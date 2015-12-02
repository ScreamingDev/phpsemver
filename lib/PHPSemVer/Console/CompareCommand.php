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
use PHPSemVer\Rules\Rule;
use PHPSemVer\Rules\RuleCollection;
use PHPSemVer\Rules\RuleFactory;
use PHPSemVer\Wrapper\Directory;
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

        $this->debug('Using wrapper ' . $wrapper);

		$previousWrapper = new $wrapper( $input->getArgument( 'previous' ) );
        $latestWrapper = new $wrapper($input->getArgument('latest'));
        if (is_dir($input->getArgument('latest'))) {
            $latestWrapper = new Directory($input->getArgument('latest'));
        }

		if ( $output->isVerbose() ) {
			$output->writeln(
				sprintf(
					'Compare "%s" with "%s"',
					$input->getArgument( 'previous' ),
					$input->getArgument( 'latest' )
				)
			);
		}

		$xmlFile = PHPSEMVER_LIB_PATH . '/PHPSemVer/Rules/SemVer2.xml';

		$previousWrapper->setExcludePattern($input->getOption('exclude'));
		$latestWrapper->setExcludePattern($input->getOption('exclude'));

		$ruleCollection = new Rule( $xmlFile );
        $this->appendIgnorePattern($xmlFile, $latestWrapper, $previousWrapper);

		$errorMessages  = $ruleCollection->processAll(
			$previousWrapper->getDataTree(),
			$latestWrapper->getDataTree()
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

		foreach ( $errorMessages as $ruleSet => $messages ) {
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

		$table->render();
		$output->writeln( 'Done!' );
	}

	public function getWrapperClass( $name ) {
		$className = '\\PHPSemVer\\Wrapper\\' . ucfirst( $name );

		if ( ! class_exists( $className ) ) {
			return false;
		}

		return $className;
	}

    /**
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
}