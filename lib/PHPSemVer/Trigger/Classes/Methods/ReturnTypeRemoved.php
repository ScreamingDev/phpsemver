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
 * @copyright 2016 Mike Pretzlaw
 * @license   http://github.com/sourcerer-mike/phpsemver/LICENSE.md MIT License
 * @link      http://github.com/sourcerer-mike/phpsemver
 */

namespace PHPSemVer\Trigger\Classes\Methods;


use PhpParser\Node\Stmt\ClassMethod;
use PHPSemVer\Constraints\FailedConstraint;
use PHPSemVer\Trigger\AbstractTrigger;

/**
 * Check if return type were removed.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2016 Mike Pretzlaw
 * @license   http://github.com/sourcerer-mike/phpsemver/LICENSE.md MIT License
 * @link      http://github.com/sourcerer-mike/phpsemver
 */
class ReturnTypeRemoved extends AbstractTrigger
{

    /**
     * Check if return type changed.
     *
     * @param ClassMethod $old
     * @param ClassMethod $new
     *
     * @return bool|null
     */
    public function handle($old, $new)
    {
        if ( ! $this->canHandle($old) || ! $this->canHandle($new)) {
            return null;
        }

        if (((string) $new->getReturnType())) {
            return false;
        }

        if (((string) $new->getReturnType()) == ((string) $old->getReturnType())) {
            return false;
        }

        $this->lastException = new FailedConstraint(
            sprintf(
                '%s::%s() return type "%s" were removed.',
                $old->getAttribute('parent')->namespacedName,
                $old->name,
                $old->getReturnType(),
                $new->getReturnType()
            )
        );

        return true;
    }

    public function canHandle($subject)
    {
        return ( $subject instanceof ClassMethod );
    }
}