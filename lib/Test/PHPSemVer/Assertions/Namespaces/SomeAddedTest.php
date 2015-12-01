<?php

namespace Test\PHPSemVer\Assertions\Namespaces;


use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Assertions\Namespaces\AddedRule;
use PHPSemVer\Assertions\Namespaces\SomeAdded;
use Test\Abstract_TestCase;

class SomeAddedTest extends Abstract_TestCase
{
    public function testItContainsErrorsWhenANamespaceIsMissing()
    {
        $this->markTestIncomplete();

        return;
        $mockBuilder = $this->getMockBuilder( get_class( new PHPBuilder() ) )
                            ->setMethods( array( 'getNamespaces' ) );

        $previousMock = $mockBuilder->getMock();
        $previousMock->expects( $this->any() )
                     ->method( 'getNamespaces' )
                     ->willReturn(
                         array()
                     );

        $latestMock = $mockBuilder->getMock();
        $latestMock->expects( $this->any() )
                   ->method( 'getNamespaces' )
                   ->willReturn(
                       array(
                           new \PDepend\Source\AST\ASTNamespace( 'missing_one' )
                       )
                   );

        $rule = new SomeAdded( $previousMock, $latestMock );
        $rule->process();

        $this->assertNotEmpty( $rule->getErrors() );

    }
}
