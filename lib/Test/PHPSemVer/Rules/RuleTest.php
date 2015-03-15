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
        $rule = new Rule( PHPSEMVER_LIB_PATH . '/PHPSemVer/Rules/SemVer2.xml' );

        $ruleSets = $rule->getAllRuleNames();
        $this->assertTrue( is_array( $ruleSets ) );

        $this->assertArrayHasKey( 'Major', $ruleSets );
        $this->assertArrayHasKey( 'Minor', $ruleSets );

        $this->assertNotEmpty( $ruleSets );
        $this->assertNotEmpty( $ruleSets[ 'Major' ] );
        $this->assertNotEmpty( $ruleSets[ 'Minor' ] );
    }

    public function testItProvidesClassesForRules()
    {
        $rule = new Rule( PHPSEMVER_LIB_PATH . '/PHPSemVer/Rules/SemVer2.xml' );

        $classes = $rule->getRuleClasses();

        $this->assertArrayHasKey( 'Major', $classes );
        $this->assertArrayHasKey( 'Minor', $classes );

        $this->assertNotEmpty( $classes[ 'Major' ] );
        $this->assertNotEmpty( $classes[ 'Minor' ] );

        $this->assertContains(
            '\\PHPSemVer\\Rules\\ClassRules\\NoneRemovedRule',
            $classes[ 'Major' ]
        );
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
