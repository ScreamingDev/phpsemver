<?php

namespace spec\PHPSemVer\Rules;

use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Rules\ClassRules\NoneRemovedRule;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RuleConfigSpec extends ObjectBehavior
{
    function it_can_be_filled_with_assertions()
    {
        $this->addAssertion( new NoneRemovedRule( new PHPBuilder(), new PHPBuilder() ) );
    }

    function it_gathers_assertions()
    {
        $this->getAssertions()->shouldReturn( array() );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType( '\\PHPSemVer\\Rules\\RuleConfig' );
    }
}
