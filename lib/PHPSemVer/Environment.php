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


use PHPSemVer\Config\RuleSet;
use PHPSemVer\Config\RuleSetCollection;
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

    public function __construct(AbstractConfig $config = null)
    {
        if ($config) {
            $this->setConfig($config);
        }
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
        $this->iterate($old, $old, $new);
        $this->iterate($new, $old, $new);

        foreach ($old->namespaces as $key => $namespace) {
            if ( ! isset( $new->namespaces[$key] )) {
                $new->namespaces[$key] = new DataNode();
            }

            $this->compareTrees($namespace, $new->namespaces[$key]);
        }

        foreach ($new->namespaces as $key => $namespace) {
            if ( isset( $old->namespaces[$key] )) {
                continue;
            }

            $old->namespaces[$key] = new DataNode();
            $this->compareTrees($old->namespaces[$key], $namespace);
        }

        return null;
    }

    protected function handleNode($subject, $old, $new)
    {
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

                $singleTrigger->handle($subject, $old, $new);

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

    private function iterate($subject, $old, $new)
    {
        foreach ($subject->classes as $class) {
            $this->handleNode($class, $old->classes, $new->classes);
        }

        foreach ($subject->functions as $func) {
            $this->handleNode($func, $old->functions, $new->functions);
        }

        foreach ($subject->usages as $use) {
            $this->handleNode($use, $old->usages, $new->usages);
        }

        foreach ($subject->namespaces as $namespace) {
            $this->handleNode($namespace, $old->namespaces, $new->namespaces);
        }
    }
}