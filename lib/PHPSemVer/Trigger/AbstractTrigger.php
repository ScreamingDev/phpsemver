<?php
/**
 * Contains abstract trigger.
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * https://opensource.org/licenses/MIT. If you did not receive a copy
 * of the PHP License and are unable to obtain it through the web, please send
 * a note to pretzlaw@gmail.com so we can mail you a copy immediately.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2016 Mike Pretzlaw
 * @license   http://github.com/sourcerer-mike/phpsemver/LICENSE.md MIT License
 * @link      http://github.com/sourcerer-mike/phpsemver
 */

namespace PHPSemVer\Trigger;

use PHPSemVer\Constraints\FailedConstraint;

/**
 * Abstract trigger.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2016 Mike Pretzlaw
 * @license   http://github.com/sourcerer-mike/phpsemver/LICENSE.md MIT License
 * @link      http://github.com/sourcerer-mike/phpsemver
 */
abstract class AbstractTrigger
{
    /**
     * Contains the last thrown exception.
     *
     * @var null|FailedConstraint
     */
    public $lastException;

    abstract public function canHandle($subject);
    abstract public function handle($old, $new);

    public function isTriggered()
    {
        return ( null != $this->lastException );
    }
}