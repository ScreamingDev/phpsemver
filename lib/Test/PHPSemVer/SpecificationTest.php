<?php

namespace Test\PHPSemVer\Rules;

use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Rules\ClassRules\NoneRemovedRule;
use PHPSemVer\Rules\NamespaceRules\AddedRule;
use PHPSemVer\Specification;
use Test\Abstract_TestCase;

class SpecificationTest extends Abstract_TestCase
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
