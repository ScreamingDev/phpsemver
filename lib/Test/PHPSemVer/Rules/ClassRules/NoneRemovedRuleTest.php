<?php

namespace PHPSemVer\Rules\NamespaceRules;


use PDepend\Source\AST\ASTClass;
use PDepend\Source\AST\ASTNamespace;
use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Rules\ClassRules\NoneRemovedRule;
use Test\Abstract_TestCase;

class NoneRemovedRuleTest extends Abstract_TestCase
{
    public function testItContainsErrorsWhenANamespaceIsMissing()
    {
        $mockBuilder = $this->getMockBuilder( get_class( new PHPBuilder() ) )
                            ->setMethods( array( 'getNamespaces' ) );

        $class     = new ASTClass( 'missing_class' );
        $namespace = new ASTNamespace( 'missing_namespace' );
        $namespace->addType( $class );

        $previousMock = $mockBuilder->getMock();
        $previousMock->expects( $this->any() )
                     ->method( 'getNamespaces' )
                     ->willReturn(
                         array(
                             $namespace
                         )
                     );

        $latestMock = $mockBuilder->getMock();
        $latestMock->expects( $this->any() )
                   ->method( 'getNamespaces' )
                   ->willReturn(
                       array()
                   );

        $rule = new NoneRemovedRule( $previousMock, $latestMock );
        $rule->process();

        $this->assertNotEmpty( $rule->getErrors() );

    }
}
