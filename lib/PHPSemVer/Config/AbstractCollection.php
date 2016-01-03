<?php
/**
 * Contains AbstractCollection class.
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

namespace PHPSemVer\Config;

use Traversable;

/**
 * Base for configuration collections.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.1.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
abstract class AbstractCollection implements \IteratorAggregate
{

    /**
     * Retrieve an external iterator.
     *
     * @link   http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     *       <b>Traversable</b>
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getChildren());
    }

    protected $children = [];

    function __construct($nodeSet)
    {
        $className = str_replace('Collection', '', get_class($this));

        foreach ($nodeSet as $node) {
            if ($node instanceof \SimpleXMLElement) {
                $node = new $className($node);
            }

            $this->children[] = $node;
        }
    }

    /**
     * Get all config areas.
     *
     * @return RuleSet[]
     */
    public function getChildren()
    {
        return $this->children;
    }
}