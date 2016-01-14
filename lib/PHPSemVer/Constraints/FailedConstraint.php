<?php
/**
 * Contains Exception.
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


namespace PHPSemVer\Constraints;
use PhpParser\Node\Stmt;

/**
 * Marks a constraint as failed.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015-2016 Mike Pretzlaw. All rights reserved.
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.2.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class FailedConstraint extends \Exception
{
    protected $value;
    protected $other;

    /**
     * Get tested entity.
     *
     * @return mixed
     */
    public function getOther()
    {
        return $this->other;
    }

    /**
     * Set tested entity.
     *
     * @param mixed $other
     */
    public function setOther($other)
    {
        $this->other = $other;
    }

    /**
     * Get tested value.
     *
     * @return Stmt
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set tested value.
     *
     * @param Stmt $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}