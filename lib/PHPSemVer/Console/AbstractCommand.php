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
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */

namespace PHPSemVer\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Basic functionality for console commands.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
abstract class AbstractCommand extends Command
{
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
    public function debug( $message )
    {
        if ( ! $this->getOutput()->isDebug() )
        {
            return null;
        }

        if ( func_num_args() > 1 )
        {
            $message = vsprintf( $message, array_slice( func_get_args(), 1 ) );
        }

        $this->getOutput()->writeln( $message );
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
     * Set output interface.
     *
     * @param OutputInterface $output
     */
    public function setOutput( $output )
    {
        $this->_output = $output;
    }

    /**
     * Get Symfony application instance.
     * @return \PHPSemVer\Console\Application
     */
    public function getApplication()
    {
        return parent::getApplication();
    }

    public function getPrdPath()
    {
        return $this->getInput()->getOption( 'prd-path' );
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
     * Set input interface.
     *
     * @param InputInterface $input
     */
    public function setInput( $input )
    {
        $this->_input = $input;
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

    public function verbose( $message )
    {
        if ( ! $this->getOutput()->isVerbose() )
        {
            return null;
        }

        if ( func_num_args() > 1 )
        {
            $message = vsprintf( $message, array_slice( func_get_args(), 1 ) );
        }

        $this->getOutput()->writeln( '<info>' . $message . '</info>' );
    }
}