<?php
/**
 * RuleSet Class.
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * https://opensource.org/licenses/MIT. If you did not receive a copy
 * of the PHP License and are unable to obtain it through the web, please send
 * a note to pretzlaw@gmail.com so we can mail you a copy immediately.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.2.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */


namespace PHPSemVer\Config\RuleSet;

use PHPSemVer\AbstractConfig;
use PHPSemVer\Config;
use PHPSemVer\Trigger\AbstractTrigger;

/**
 * Wrapper for the config node "RuleSet/Trigger".
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.2.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 *
 * @method string getName()
 */
class Trigger extends AbstractConfig
{
    const XPATH = '//PHPSemVer/RuleSet/Trigger';
    protected $instances = null;

    /**
     * Get all trigger instances from the config.
     *
     * @return AbstractTrigger[]
     */
    public function getInstances()
    {
        if (null === $this->instances) {
            $this->instances = [];

            foreach ($this->getAll() as $className) {
                $className = '\\PHPSemVer\\Trigger\\' . str_replace('/', '\\', $className);

                $this->instances[] = new $className();
            }
        }

        return $this->instances;
    }

    /**
     * Turns all inner trigger into one flat array.
     *
     * @return array
     */
    public function getAll()
    {
        return $this->resolveAll($this->getXml());
    }

    protected function resolveAll(\SimpleXMLElement $node, $prefix = '')
    {
        $resolved = [];
        foreach ($node->children() as $childNode) {
            $nodeName = $prefix.$childNode->getName();
            if ( ! $childNode->count()) {
                $resolved[] = $nodeName;
                continue;
            }

            $resolved = array_merge(
                $resolved,
                $this->resolveAll($childNode, $nodeName.'/')
            );
        }

        return $resolved;
    }
}