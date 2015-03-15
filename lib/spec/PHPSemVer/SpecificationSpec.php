<?php

namespace spec\PHPSemVer;

use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Rules\ClassRules\NoneRemovedRule;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SpecificationSpec extends ObjectBehavior
{
    function it_can_be_filled_with_assertions()
    {
        $this->addAssertion( new NoneRemovedRule( new PHPBuilder(), new PHPBuilder() ) );
    }

    function it_can_be_filled_with_rule_sets()
    {
        $this->addRuleSet( 'minor' )->shouldReturn( null );
    }

    function it_contains_rule_sets()
    {
        $this->getRuleSets()->shouldReturn( array() );
    }

    function it_gathers_assertions()
    {
        $this->getAssertions()->shouldReturn( array() );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType( '\\PHPSemVer\\Specification' );
    }
}
