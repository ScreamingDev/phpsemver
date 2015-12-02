<?php
/**
 * Contains abstract assertion.
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


use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\DataTree\DataNode;

/**
 * Abstract Assertion.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class AbstractAssertion
{

    protected $_errors = array();
    protected $_latest;
    protected $_previous;

    /**
     * Create new assertion.
     *
     * @param DataNode $previous
     * @param DataNode $latest
     */
    public function __construct( DataNode $previous, DataNode $latest )
    {
        $this->_previous = $previous;
        $this->_latest   = $latest;
    }

    public function appendMessage( $message )
    {
        $this->_errors[ ] = new Message( get_class( $this ), $message );
    }

    /**
     * Get all errors.
     *
     * @return Message[]
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Get DataNode for latest version.
     *
     * @return DataNode
     */
    public function getLatest()
    {
        return $this->_latest;
    }

    /**
     * Get DataNode for previous version.
     *
     * @return DataNode
     */
    public function getPrevious()
    {
        return $this->_previous;
    }


}