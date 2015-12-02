<?php
/**
 * Specification class.
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

namespace PHPSemVer;

use PHPSemVer\Assertions\AbstractAssertion;
use PHPSemVer\Rules\RuleSet;

/**
 * Speification class.
 *
 * Very long desc.
 *
 * @author  Mike Pretzlaw <pretzlaw@gmail.com>
 * @license https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link    https://github.com/sourcerer-mike/phpsemver
 */
class Specification
{

    protected $_assertions = array();

    protected $_ruleSets = array();


    /**
     * Add assertion to rule set
     * @param AbstractAssertion $assertionObject Assertion to test against.
     */
    public function addAssertion(AbstractAssertion $assertionObject)
    {
        $hash = spl_object_hash($assertionObject);

        $this->_assertions[$hash] = $assertionObject;

    }


    public function getAssertions()
    {
        return $this->_assertions;

    }


    public function getRuleSets()
    {
        return $this->_ruleSets;

    }


    /**
     * Import and replace from XML rule set.
     *
     * @param \SimpleXMLElement $xml
     *
     * @throws \Exception
     */
    public function updateFromXml($xml)
    {
        foreach ($xml->xpath('//RuleSet') as $ruleSetXml) {
            if ( ! $ruleSetXml->attributes()
                 || ! $ruleSetXml->attributes()->name
            ) {
                throw new \Exception(
                    'Please provide a valid ruleSet name-attribute. Found invalid.'
                );
            }

            $ruleSet = new RuleSet($ruleSetXml->attributes()->name);
            $ruleSet->updateFromXml($ruleSetXml);

            $this->addRuleSet($ruleSet);
        }

    }


    public function addRuleSet($ruleSet)
    {
        $this->_ruleSets[] = $ruleSet;

    }


}
