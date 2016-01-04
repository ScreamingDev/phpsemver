<?php
/**
 * Contains abstract command.
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * https://opensource.org/licenses/MIT. If you did not receive a copy
 * of the PHP License and are unable to obtain it through the web, please send
 * a note to pretzlaw@gmail.com so we can mail you a copy immediately.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.1.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */

namespace PHPSemVer\Console;

use PHPSemVer\Config;
use PHPSemVer\Environment;
use PHPSemVer\Wrapper\AbstractWrapper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Basic functionality for console commands.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.1.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
abstract class AbstractCommand extends Command
{
    protected $_config;
    protected $_input;
    protected $_output;
    protected $_outputDocument;
    protected $environment;
    protected $latestWrapper;
    protected $previousWrapper;

    /**
     * Add pattern to exclude files.
     *
     * @param Config          $config
     * @param AbstractWrapper $latestWrapper
     * @param AbstractWrapper $previousWrapper
     */
    protected function appendIgnorePattern($config, $latestWrapper, $previousWrapper)
    {
        $config = $config->getXml();

        $ignorePattern = [];
        if (isset($config->Filter)) {
            if (isset($config->Filter->Blacklist)) {
                foreach ($config->Filter->Blacklist as $node) {
                    if ( ! isset($node->Pattern)) {
                        continue;
                    }

                    foreach ($node->Pattern as $pattern) {
                        $ignorePattern[] = (string)$pattern;
                    }
                }
            }
        }

        $latestWrapper->setExcludePattern($ignorePattern);
        $previousWrapper->setExcludePattern($ignorePattern);
    }

    protected function compareTrees()
    {

        $prevTree = $this->parseFiles(
            $this->getPreviousWrapper(),
            $this->getInput()->getArgument('previous') . ': '
        );

        $newTree = $this->parseFiles(
            $this->getLatestWrapper(),
            $this->getInput()->getArgument('latest') . ': '
        );

        $this->getOutput()->write('');

        $time = microtime(true);

        $this->getEnvironment()->compareTrees($prevTree, $newTree);

        $this->verbose(
            sprintf(
                "\rCompared within %0.2f seconds",
                microtime(true) - $time
            )
        );
    }

    /**
     * Print debug message.
     *
     * Prints debug message,
     * if debug mode is enabled (via -vvv).
     *
     * @param $message
     *
     * @return null
     */
    public function debug($message)
    {
        if ( ! $this->getOutput()->isDebug()) {
            return null;
        }

        if (func_num_args() > 1) {
            $message = vsprintf($message, array_slice(func_get_args(), 1));
        }

        $this->getOutput()->writeln($message);
    }

    protected function fetchConfig()
    {
        $xmlFile = $this->resolveConfigFile($this->getInput()->getOption('ruleSet'));

        $this->debug('Using config-file ' . $xmlFile);

        $this->_config = new Config(simplexml_load_file($xmlFile));

        if ($this->getOutput()->isVerbose()) {
            $this->printConfig();
        }
    }

    /**
     * Get Symfony application instance.
     *
     * @return \PHPSemVer\Console\Application
     */
    public function getApplication()
    {
        return parent::getApplication();
    }

    /**
     * Get configuration of this command.
     *
     * @return Config
     */
    public function getConfig()
    {
        if (!$this->_config) {
            $this->fetchConfig();
        }

        return $this->_config;
    }

    /**
     * Get environment object.
     *
     * @param Config $config Deprecated 4.0.0, command config will be used instead.
     *
     * @return Environment
     */
    public function getEnvironment($config = null)
    {
        if ( ! $this->environment) {
            if (null == $config) {
                $config = $this->getConfig();
            }

            $this->environment = new Environment($config);
        }

        return $this->environment;
    }

    /**
     * Get input interface.
     *
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->_input;
    }

    /**
     * Get wrapper for VCS.
     *
     * @return AbstractWrapper
     */
    protected function getLatestWrapper()
    {
        if ($this->latestWrapper) {
            return $this->latestWrapper;
        }

        $input = $this->getInput();

        $this->latestWrapper = $this->getWrapperInstance(
            $input->getArgument('latest'),
            $input->getOption('type')
        );

        return $this->latestWrapper;
    }

    /**
     * Get output interface.
     *
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->_output;
    }

    /**
     * Get the wrapper for the VCS.
     *
     * @return AbstractWrapper
     */
    protected function getPreviousWrapper()
    {
        if ($this->previousWrapper) {
            return $this->previousWrapper;
        }

        $input = $this->getInput();

        $this->previousWrapper = $this->getWrapperInstance(
            $input->getArgument('previous'),
            $input->getOption('type')
        );

        return $this->previousWrapper;
    }

    public function getWrapperClass($name)
    {
        $className = '\\PHPSemVer\\Wrapper\\' . ucfirst($name);

        if ( ! class_exists($className)) {
            return false;
        }

        return $className;
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
    protected function getWrapperInstance($base, $type = 'Directory')
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

        $this->debug('Using wrapper "' . $wrapper . '" for "' . $base . '"');

        return new $wrapper($base);
    }

    protected function configure()
    {
        $this->addOption(
            'exclude',
            null,
            InputOption::VALUE_OPTIONAL,
            'Exclude files containing the given regexp (extends the XML config).',
            ''
        );

        $this->addOption(
            'type',
            't',
            InputOption::VALUE_OPTIONAL,
            'Type of given targets',
            'git'
        );

        $this->addOption(
            'ruleSet',
            'R',
            InputOption::VALUE_OPTIONAL,
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


    protected function initialize(
        InputInterface $input,
        OutputInterface $output
    ) {
        parent::initialize($input, $output);

        $this->setInput($input);
        $this->setOutput($output);

        $this->verbose(
            'Comparing %s "%s" with %s "%s" using "%s" ...',
            $input->getOption( 'type' ),
            $input->getArgument( 'latest' ),
            $input->getOption( 'type' ),
            $input->getArgument( 'previous' ),
            $input->getOption( 'ruleSet' )
        );

        $this->fetchConfig();

        $this->getPreviousWrapper()->addExcludePattern($input->getOption('exclude'));
        $this->getLatestWrapper()->addExcludePattern($input->getOption('exclude'));

        $this->appendIgnorePattern($this->getConfig(), $this->getPreviousWrapper(), $this->getLatestWrapper());
    }

    /**
     * Generate environment from config.
     *
     * @param $config
     *
     * @deprecated 4.0.0 Use ::getEnvironment instead.
     *
     * @return Environment
     */
    protected function makeEnvironment($config)
    {
        return $this->getEnvironment($config);
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

    /**
     * Print debug information of the used config.
     *
     * @param Config          $config
     * @param OutputInterface $output
     */
    protected function printConfig()
    {
        $config = $this->getConfig();
        $output = $this->getOutput();

        foreach ($config->ruleSet()->getChildren() as $ruleSet) {
            $output->writeln('Using rule set ' . $ruleSet->getName());

            if ( ! $output->isDebug()) {
                continue;
            }

            if ( ! $ruleSet->trigger()) {
                $output->writeln('  No triggers found.');
                continue;
            }

            foreach ($ruleSet->trigger()->getAll() as $singleTrigger) {
                $output->writeln('  Contains trigger ' . $singleTrigger);
            }
        }
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

        if (file_exists($ruleSet . '.xml')) {
            return $ruleSet;
        }

        $defaultPath = PHPSEMVER_LIB_PATH . '/PHPSemVer/Rules/';
        if (file_exists($defaultPath . $ruleSet)) {
            return $defaultPath . $ruleSet;
        }

        if (file_exists($defaultPath . $ruleSet . '.xml')) {
            return $defaultPath . $ruleSet . '.xml';
        }

        throw new \InvalidArgumentException(
            'Could not find rule set: ' . $ruleSet
        );
    }

    /**
     * Set configuration for this command.
     *
     * @param mixed $config
     */
    public function setConfig($config)
    {
        $this->_config = $config;
    }

    /**
     * Set input interface.
     *
     * @param InputInterface $input
     */
    public function setInput($input)
    {
        $this->_input = $input;
    }

    /**
     * Set output interface.
     *
     * @param OutputInterface $output
     */
    public function setOutput($output)
    {
        $this->_output = $output;
    }

    public function verbose($message)
    {
        if ( ! $this->getOutput()->isVerbose()) {
            return null;
        }

        if (func_num_args() > 1) {
            $message = vsprintf($message, array_slice(func_get_args(), 1));
        }

        $this->getOutput()->writeln('<info>' . $message . '</info>');
    }
}