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
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.1.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */


namespace PHPSemVer\Trigger\Classes\Methods;


use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeAbstract;
use PHPSemVer\Constraints\FailedConstraint;
use PHPSemVer\Trigger\AbstractTrigger;

/**
 * Check if class is added.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.1.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class IsAdded extends AbstractTrigger
{
    /**
     * Check if class method has been added.
     *
     * @param null $old
     * @param ClassMethod $new
     *
     * @return bool|null
     */
    public function handle($old, $new)
    {
        $this->lastException = null;

        if ( ! $this->canHandle($new)) {
            return null;
        }

        if ($old) {
            return false;
        }

        $this->lastException = new FailedConstraint(
            sprintf(
                '%s::%s() added',
                $new->getAttribute('parent')->namespacedName,
                $new->name
            )
        );

        return true;
    }

    public function canHandle($subject)
    {
        return ( $subject instanceof ClassMethod );
    }
}