<?php
/**
 * Contains messaging class.
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

namespace PHPSemVer\Assertions;

/**
 * Single message from assertions.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class Message
{
    protected $_message;
    protected $_rule;

    /**
     * Create new message.
     *
     * @param string $rule
     * @param string $message
     */
    public function __construct( $rule, $message )
    {
        $this->_rule    = $rule;
        $this->_message = $message;
    }

    /**
     * Get message text.
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Get according rule.
     *
     * @return mixed
     */
    public function getRule()
    {
        return $this->_rule;
    }


}