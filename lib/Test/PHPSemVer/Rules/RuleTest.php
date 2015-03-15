<?php

namespace Test\PHPSemVer\Rules;


use PHPSemVer\Rules\Rule;
use Test\Abstract_TestCase;

class RuleTest extends Abstract_TestCase
{
    public function testItCanConvertRuleNamesToClasses()
    {
        $rule = new Rule( PHPSEMVER_LIB_PATH . '/PHPSemVer/Rules/SemVer2.xml' );

        $class = $rule->ruleNameToClass( 'ClassRules.NoneRemoved' );

        $this->assertNotEmpty( $class );
        $this->assertTrue( class_exists( $class ) );
    }

    public function testItProvidesAllRuleSets()
    {
        $this->markTestIncomplete();
    }

    /**
     * @expectedException \Exception
     */
    public function testItThrowsAnExceptionWithMissingClassForRuleName()
    {
        $rule = new Rule( PHPSEMVER_LIB_PATH . '/PHPSemVer/Rules/SemVer2.xml' );

        $rule->ruleNameToClass( 'Foo.Bar.Baz' );
    }

    /**
     * @expectedException \Exception
     */
    public function testItThrowsErrorWhenFileDoesNotExists()
    {
        $rule = new Rule( null );
    }
}
