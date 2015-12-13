<?php
/**
 * Contains constraint.
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

namespace PHPSemVer\Constraints;


use PhpParser\Node\Stmt;

/**
 * Check if statement exists in list via name.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class Contains extends AbstractConstraint implements ConstraintInterface
{
    public function __construct($needle = null)
    {
        parent::__construct($needle);
    }

    /**
     * Searches for the value in the given node.
     *
     * @param Stmt $other
     *
     * @return bool True if found.
     * @throws FailedConstraint
     */
    public function evaluate($other)
    {
        if (isset( $other->stmts )) {
            $other = $other->stmts;
        }

        return $this->searchInList($other);

        throw new \InvalidArgumentException(sprintf('Can not handle "%s".', get_class($other)));
    }

    protected function searchInList($stmtList)
    {
        $name  = (string) $this->getValue()->name;
        $class = get_class($this->getValue());
        foreach ($stmtList as $node) {
            if (get_class($node) != $class) {
                continue;
            }

            if ((string) $node->name == $name) {
                return true;
            }
        }

        throw new FailedConstraint(sprintf('"%s" not found', $name));
    }
}