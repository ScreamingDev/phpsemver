<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 15.03.15
 * Time: 10:23
 */

namespace Test\PHPSemVer\Rules;


use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Rules\ClassRules\NoneRemovedRule;
use PHPSemVer\Rules\NamespaceRules\AddedRule;
use PHPSemVer\Rules\RuleConfig;
use Test\Abstract_TestCase;

class RuleConfigTest extends Abstract_TestCase
{
    public function testItGathersAssertions()
    {
        $ruleConfig = new RuleConfig();

        $value = array( 1, 2, 3 );
        $this->setProperty( $ruleConfig, '_assertions', $value );

        $this->assertEquals( $value, $ruleConfig->getAssertions() );
    }

    public function testItWontHaveDuplicateAssertionsStored()
    {
        $builder = new PHPBuilder();
        $rule1   = new NoneRemovedRule( $builder, $builder );
        $rule2   = new AddedRule( $builder, $builder );

        $ruleConfig = new RuleConfig();
        $ruleConfig->addAssertion( $rule2 );
        $ruleConfig->addAssertion( $rule1 );
        $ruleConfig->addAssertion( $rule2 );
        $ruleConfig->addAssertion( $rule1 );

        $this->assertEquals(
            array(
                $rule2,
                $rule1
            ),
            array_values( $ruleConfig->getAssertions() )
        );
    }
}
