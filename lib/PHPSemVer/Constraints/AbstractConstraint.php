<?php
/**
 * Contians abstract constraint.
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
 * Basic functionality for constraints.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
abstract class AbstractConstraint
{
    protected $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * Evaluates the constraint.
     *
     * @param mixed $other Value or object to evaluate.
     *
     * @return mixed
     *
     * @throws FailedConstraint
     */
    abstract public function evaluate($other);

    /**
     * Get base value.
     *
     * All checks are done against this value.
     *
     * @return Stmt
     */
    public function getValue()
    {
        return $this->value;
    }
}