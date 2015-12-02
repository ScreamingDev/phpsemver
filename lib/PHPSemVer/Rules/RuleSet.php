<?php
/**
 * RuleSet class.
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

namespace PHPSemVer\Rules;

use PHPSemVer\Assertions\AbstractAssertion;

/**
 * Rule set with multiple assertions.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class RuleSet
{
    protected $_assertions = array();
    protected $_name;

    public function __construct( $name )
    {
        $this->_name = $name;
    }

    public function addAssertion( AbstractAssertion $assertion )
    {
        $hash                       = spl_object_hash( $assertion );
        $this->_assertions[ $hash ] = $assertion;
    }

    public function getAssertions()
    {
        return $this->_assertions;
    }

    public function getName()
    {
        return $this->_name;
    }

    /**
     * Parse XML config.
     *
     * @param \SimpleXMLElement $ruleSetXml
     *
     * @throws \Exception
     */
    public function updateFromXml( $ruleSetXml )
    {
        foreach ( $ruleSetXml->xpath( 'Assertions' ) as $assertion )
        {
            $namespace = '\\PHPSemVer\\Assertions\\';
            foreach ( $assertion->children() as $section) {
                foreach ($section->children() as $className) {
                    $className = $namespace . $section->getName() . '\\' . $className->getName();

                    if (!class_exists($className)) {
                        throw new \Exception(
                            sprintf(
                                'Please provide valid assertions. '.
                                'Could not find class "%s".',
                                $className
                            )
                        );
                    }
                }
            }
        }
    }
}
