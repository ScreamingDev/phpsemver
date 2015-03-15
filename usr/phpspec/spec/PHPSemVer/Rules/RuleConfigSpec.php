<?php

namespace spec\PHPSemVer\Rules;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RuleConfigSpec extends ObjectBehavior
{
    function it_gathers_assertions()
    {
        $this->getAssertions()->shouldReturn( array() );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType( '\\PHPSemVer\\Rules\\RuleConfig' );
    }
}
