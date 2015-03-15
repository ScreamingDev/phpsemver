<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 15.03.15
 * Time: 10:23
 */

namespace Test\PHPSemVer\Rules;


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
}
