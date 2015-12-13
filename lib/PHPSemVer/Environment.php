<?php
/**
 * Contains Environment Class.
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

namespace PHPSemVer;


use PHPSemVer\DataTree\DataNode;

/**
 * Environment to work with.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class Environment
{
    /**
     * Configuration for this environment.
     *
     * @var AbstractConfig
     */
    protected $config;
    protected $errorMessages = [];

    public function __construct(AbstractConfig $config = null)
    {
        if ($config) {
            $this->setConfig($config);
        }
    }

    public function appendErrorMessage($exception, $ruleSet = 'unknown')
    {
        if ( ! isset( $this->errorMessages[$ruleSet] )) {
            $this->errorMessages[$ruleSet] = [];
        }

        $this->errorMessages[$ruleSet][] = $exception;
    }

    /**
     * Compare two parsed AST.
     *
     * @param DataNode $old
     * @param DataNode $new
     *
     * @return null
     */
    public function compareTrees(DataNode $old, DataNode $new)
    {
        return null;
    }

    /**
     * Get environment configuration.
     *
     * @return AbstractConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set environment configuration.
     *
     * @param AbstractConfig $config
     */
    public function setConfig(AbstractConfig $config)
    {
        $this->config = $config;
    }

    public function getErrorMessages()
    {
        return $this->errorMessages;
    }
}