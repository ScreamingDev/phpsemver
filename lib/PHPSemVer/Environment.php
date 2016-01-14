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
 * @copyright 2015-2016 Mike Pretzlaw. All rights reserved.
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.2.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */

namespace PHPSemVer;


use PhpParser\Node;
use PHPSemVer\Config\RuleSet;
use PHPSemVer\Config\RuleSetCollection;

/**
 * Environment to work with.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015-2016 Mike Pretzlaw. All rights reserved.
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.2.0/LICENSE.md MIT License
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

    public function __construct(AbstractConfig $config = null)
    {
        if ($config) {
            $this->setConfig($config);
        }
    }

    /**
     * Compare two parsed AST.
     *
     * @param Node[] $previous
     * @param Node[] $latest
     *
     * @return null
     */
    public function compareTrees(array $previous, array $latest)
    {
        $sum = array_merge(
            array_keys($previous),
            array_keys($latest)
        );

        $sum = array_filter($sum, 'is_string');
        $sum = array_unique($sum);
        asort($sum);

        foreach ($sum as $key) {
            $old = null;
            if (isset( $previous[$key] )) {
                $old = $previous[$key];
            }

            $new = null;
            if (isset( $latest[$key] )) {
                $new = $latest[$key];
            }

            $this->handleNode($old, $new);

            if ($old && $new) {
                foreach ($old->getSubNodeNames() as $subNode) {
                    if ( ! is_array($old->$subNode)) {
                        continue;
                    }

                    $this->compareTrees($old->$subNode, $new->$subNode);
                }
            }
        }

        return null;
    }

    protected function handleNode($old, $new)
    {
        $subject = $old;
        if ( ! $old) {
            $subject = $new;
        }

        /* @var RuleSetCollection $ruleSetCollection */
        $ruleSetCollection = $this->getConfig()->ruleSet();
        foreach ($ruleSetCollection->getChildren() as $ruleSet) {
            /* @var RuleSet\Trigger $trigger */
            $trigger = $ruleSet->trigger();


            if ( ! $trigger) {
                // no trigger inside: next!
                continue;
            }

            foreach ($trigger->getInstances() as $singleTrigger) {
                if ( ! $singleTrigger->canHandle($subject)) {
                    continue;
                }

                $singleTrigger->lastException = null;
                $singleTrigger->handle($old, $new);

                if ( ! $singleTrigger->isTriggered()) {
                    continue;
                }

                $ruleSet->appendErrorMessage($singleTrigger->lastException);
            }
        }
    }

    /**
     * Get environment configuration.
     *
     * @return Config
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
}