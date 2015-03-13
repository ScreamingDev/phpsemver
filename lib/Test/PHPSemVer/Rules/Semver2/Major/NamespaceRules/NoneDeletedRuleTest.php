<?php

namespace PHPSemVer\Rules\Semver2\Major\NamespaceRules;


use PDepend\Source\Language\PHP\PHPBuilder;
use Test\Abstract_TestCase;

class NoneDeletedRuleTest extends Abstract_TestCase
{
    public function testItContainsErrorsWhenANamespaceIsMissing()
    {
        $mockBuilder = $this->getMockBuilder( get_class( new PHPBuilder() ) )
                            ->setMethods( array( 'getNamespaces' ) );

        $previousMock = $mockBuilder->getMock();
        $previousMock->expects( $this->any() )
                     ->method( 'getNamespaces' )
                     ->willReturn(
                         array(
                             new \PDepend\Source\AST\ASTNamespace( 'missing_one' )
                         )
                     );

        $latestMock = $mockBuilder->getMock();
        $latestMock->expects( $this->any() )
                   ->method( 'getNamespaces' )
                   ->willReturn(
                       array()
                   );

        $rule = new NoneDeletedRule( $previousMock, $latestMock );
        $rule->process();

        $this->assertNotEmpty( $rule->getErrors() );

    }
}
