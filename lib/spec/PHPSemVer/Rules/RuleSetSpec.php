<?php

namespace spec\PHPSemVer\Rules;

use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Rules\ClassRules\NoneRemovedRule;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Subject;
use Prophecy\Argument;

class RuleSetSpec extends ObjectBehavior
{
    function it_can_be_filled_with_assertions()
    {
        $this->beConstructedWith( 'RuleName' );
        $this->addAssertion( new NoneRemovedRule( new PHPBuilder(), new PHPBuilder() ) )->shouldReturn( null );
    }

    function it_gathers_assertions()
    {
        $this->beConstructedWith( 'RuleName' );
        $this->getAssertions()->shouldReturn( array() );
    }

    function it_has_a_name()
    {
        $this->beConstructedWith( 'RuleName123' );
        $this->getName()->shouldReturn( 'RuleName123' );
    }

    function it_is_initializable()
    {
        $this->beConstructedWith( 'RuleName' );
        $this->shouldHaveType( 'PHPSemVer\Rules\RuleSet' );
    }
}
