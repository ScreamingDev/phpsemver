<?php

namespace spec\PHPSemVer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SpecificationSpec extends ObjectBehavior
{
    function it_can_be_filled_with_rule_sets()
    {
        $this->addRuleSet( 'minor' )->shouldReturn( null );
    }

    function it_can_read_from_xml_string()
    {
        $this->updateFromXmlString( '<?xml version="1.0" ?><phpsemver></phpsemver>' )->shouldReturn( null );
    }

    function it_contains_rule_sets()
    {
        $this->getRuleSets()->shouldReturn( array() );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType( '\\PHPSemVer\\Specification' );
    }
}
