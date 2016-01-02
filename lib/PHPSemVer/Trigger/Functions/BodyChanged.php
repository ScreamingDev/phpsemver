<?php
/**
 * Contains trigger.
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


namespace PHPSemVer\Trigger\Functions;

use PhpParser\Node\Stmt\Function_;
use PHPSemVer\Constraints\FailedConstraint;
use PHPSemVer\Trigger\AbstractTrigger;

/**
 * Check if body of method has changed.
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
class BodyChanged extends AbstractTrigger
{

    /**
     * Check if body of function has changed.
     *
     * @param Function_ $old
     * @param Function_ $new
     *
     * @return bool
     */
    public function handle($old, $new)
    {
        if ( ! $this->canHandle($old) || ! $this->canHandle($new)) {
            return null;
        }

        if ($old->stmts == $new->stmts) {
            return false;
        }

        $this->lastException = new FailedConstraint(
            sprintf(
                '%s() body changed.',
                $new->name
            )
        );

        return true;
    }

    public function canHandle($subject)
    {
        return ( $subject instanceof Function_ );
    }
}