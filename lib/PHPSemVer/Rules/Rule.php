<?php
/**
 * Rule class.
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


use DesignPattern\Structural\AbstractComposite;
use PHPSemVer\Assertions\AbstractAssertion;
use PHPSemVer\DataTree\DataNode;

/**
 * Rule wrapper.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class Rule
{
    protected $_fileName;

    function __construct($targetRule)
    {
        if ( ! file_exists($targetRule)) {
            throw new \Exception(
                sprintf(
                    'Please provide a valid rule name. ' .
                    'Could not find "%s".',
                    $targetRule
                )
            );
        }

        $this->_fileName = $targetRule;
    }

    /**
     * Process all rules.
     *
     * @param DataNode $previous
     * @param DataNode $latest
     *
     * @return array
     */
    public function processAll(
        DataNode $previous, DataNode $latest
    ) {
        $errorMessages = array();
        foreach ($this->getRuleClasses() as $assertionName => $classes) {
            if ( ! isset($errorMessages[$assertionName])) {
                $errorMessages[$assertionName] = [];
            }

            foreach ($classes as $className) {
                /* @var AbstractAssertion $singleRule */
                $singleRule = new $className($previous, $latest);
                $singleRule->process();

                $errorMessages[$assertionName] = array_merge(
                    $errorMessages[$assertionName],
                    $singleRule->getErrors()
                );
            }
        }

        return $errorMessages;
    }

    public function getRuleClasses()
    {
        $ruleClasses = array();

        foreach ($this->getAllRuleNames() as $ruleSet => $refs) {
            if ( ! isset($ruleClasses[$ruleSet])) {
                $ruleClasses[$ruleSet] = [];
            }

            foreach ($refs as $ruleName) {
                $class                   = $this->ruleNameToClass($ruleName);
                $ruleClasses[$ruleSet][] = $class;
            }
        }

        return $ruleClasses;
    }

    public function getAllRuleNames()
    {
        $xml = simplexml_load_file($this->_fileName);

        $ruleSet = array();

        foreach ($xml->xpath('//RuleSet') as $singleRuleSet) {
            $section = (string)$singleRuleSet->attributes()->name;
            foreach ($singleRuleSet->xpath('assertions') as $assertions) {
                foreach ($assertions->children() as $name => $rules) {
                    foreach ($rules as $ruleName => $settings) {
                        $ruleSet[$section][] = (string)$name . '\\' . $ruleName;
                    }

                }
            }
        }

        return $ruleSet;
    }

    public function ruleNameToClass($ruleName)
    {
        $segments = explode('.', $ruleName);
        $class    = '\\PHPSemVer\\Assertions\\' . implode('\\', $segments);

        if ( ! class_exists($class)) {
            throw new \Exception(
                sprintf(
                    'Invalid rule "%s" in "%s" (class "%s" not found).',
                    $ruleName,
                    $this->_fileName,
                    $class
                )
            );
        }

        return $class;
    }
}