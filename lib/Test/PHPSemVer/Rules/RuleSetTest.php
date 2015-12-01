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
use PHPSemVer\Assertions\Functions\SomeAdded;
use PHPSemVer\Assertions\Methods\SomeRemoved;
use PHPSemVer\Assertions\Namespaces\AddedRule;
use PHPSemVer\DataTree\DataNode;
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
        $data  = new DataNode();
        $rule1 = new SomeRemoved($data, $data);
        $rule2 = new SomeAdded($data, $data);

        $ruleConfig = new RuleSet( 'foo' );
        $ruleConfig->addAssertion( $rule1 );
        $ruleConfig->addAssertion( $rule2 );
        $ruleConfig->addAssertion( $rule1 );
        $ruleConfig->addAssertion( $rule2 );

        $this->assertCount( 2, $ruleConfig->getAssertions() );
        $this->assertContains( $rule1, $ruleConfig->getAssertions() );
        $this->assertContains( $rule2, $ruleConfig->getAssertions() );
    }
}
