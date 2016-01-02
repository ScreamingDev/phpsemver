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


namespace PHPSemVer\Trigger\Classes;


use PhpParser\Node\Stmt\Class_;
use PHPSemVer\Constraints\Contains;
use PHPSemVer\Constraints\FailedConstraint;
use PHPSemVer\Trigger\AbstractTrigger;

/**
 * Check if class is added.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class IsAdded extends AbstractTrigger
{
    public function canHandle($subject)
    {
        return ( $subject instanceof Class_ );
    }

    public function handle($subject, $old, $new)
    {
        if ( ! $this->canHandle($subject) ) {
            return;
        }

        $this->lastException = null;

        $constraint = new Contains($subject);

        try {
            $constraint->evaluate($old);
        } catch (FailedConstraint $e) {
            $this->lastException = new FailedConstraint(
                sprintf(
                    '%s added.',
                    $e->getValue()->namespacedName
                )
            );

            return true;
        }

        return false;
    }
}