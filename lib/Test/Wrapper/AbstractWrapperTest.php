<?php

namespace Test\PHPSemVer\Wrapper;


use Test\Abstract_TestCase;

class AbstractWrapperTest extends Abstract_TestCase {
	public function testItContainsExceptionsOnParserFailures() {
		$parserMock = $this->getMockBuilder(
			'\\PDepend\\Source\\Language\\PHP\\PHPParserGeneric'
		)
		                   ->disableOriginalConstructor()
		                   ->setMethods( array( 'parse' ) )
		                   ->getMock();

		$exception = new \PDepend\Source\Parser\ParserException();

		$parserMock->expects( $this->any() )
		           ->method( 'parse' )
		           ->willThrowException( $exception );

		$mock = $this->getMockForAbstractClass(
			$this->getTargetClass(),
			array( 'foo' ),
			'',
			false,
			false,
			true,
			array(
				'getParser',
				'getAllFileNames',
				'getPath',
			)
		);

		$mock->expects( $this->any() )
		     ->method( 'getAllFileNames' )
		     ->willReturn( array( 'foo.php' ) );

		$mock->expects( $this->any() )
		     ->method( 'getPath' )
		     ->willReturn( 'foo.php' );


		$mock->expects( $this->any() )
		     ->method( 'getParser' )
		     ->withAnyParameters()
		     ->willReturn( $parserMock );

		$mock->getBuilder();

		$failures = $mock->getParserExceptions();

		$this->assertSame( 1, count( $failures ) );

		$this->assertContains( $exception, $failures );
	}

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testItThrowsExceptionIfBaseIsNotGiven()
    {
        $mock = $this->getMockForAbstractClass(
            $this->getTargetClass(),
            array( null )
        );
    }
}
