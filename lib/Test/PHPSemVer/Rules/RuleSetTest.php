<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 15.03.15
 * Time: 23:07
 */

namespace PHPSemVer\Rules;


use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Assertions\Classes\NoneRemovedRule;
use PHPSemVer\Assertions\Namespaces\AddedRule;
use PHPSemVer\Specification;
use Test\Abstract_TestCase;

class RuleSetTest extends Abstract_TestCase
{
    public function testItGathersAssertions()
    {
        $ruleConfig = new Specification();

        $value = array( 1, 2, 3 );
        $this->setProperty( $ruleConfig, '_assertions', $value );

        $this->assertEquals( $value, $ruleConfig->getAssertions() );
    }

    public function testItWontHaveDuplicateAssertionsStored()
    {
        $builder = new PHPBuilder();
        $rule1   = new NoneRemovedRule( $builder, $builder );
        $rule2   = new AddedRule( $builder, $builder );

        $ruleConfig = new Specification();
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
