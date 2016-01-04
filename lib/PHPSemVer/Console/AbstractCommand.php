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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
     * @return mixed
     */
    public function getConfig()
    {
        return $this->_config;
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
     * Get output interface.
     *
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->_output;
    }

    protected function initialize(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->setInput($input);
        $this->setOutput($output);

        $this->fetchConfig();

        parent::initialize($input, $output);
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