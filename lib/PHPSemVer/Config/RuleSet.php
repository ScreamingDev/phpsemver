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
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.1.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */


namespace PHPSemVer\Config;

use PHPSemVer\AbstractConfig;
use PHPSemVer\Config;
use PHPSemVer\Config\RuleSet\Trigger;

/**
 * Config > RuleSet.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.1.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 *
 * @method string getName()
 * @method Trigger trigger()
 */
class RuleSet extends AbstractConfig
{
    const XPATH = '//PHPSemVer/RuleSet';

    protected $attributes
        = [
            'name' => ''
        ];

    protected $errorMessages = [];

    public function appendErrorMessage($exception)
    {
        $this->errorMessages[] = $exception;
    }

    public function getErrorMessages()
    {
        return $this->errorMessages;
    }
}