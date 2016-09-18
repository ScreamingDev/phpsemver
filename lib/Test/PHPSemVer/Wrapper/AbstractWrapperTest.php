<?php

namespace Test\PHPSemVer\Wrapper;


use PhpParser\Error;
use PHPSemVer\Wrapper\AbstractWrapper;
use Test\Abstract_TestCase;

class AbstractWrapperTest extends Abstract_TestCase {
	/**
	 * The PHP parser throws errors when it is unable to understand the code.
	 * All those errors are fetched and stored in the errorCollection.
	 */
	public function testItCollectsParserErrorsInsteadOfThrowingThem() {
		$wrapper = new AbstractWrapperTest_Subject( $this->getResourcePath( 'v2' ) );

		$builder = $this->getMockBuilder( get_class( $wrapper ) )
		                ->disableOriginalConstructor()
		                ->setProxyTarget( $wrapper )
		                ->setMethods( [ 'getAllFileNames' ] );

		$failedFilePath = $this->getResourcePath( 'v2/fileNotParsable.php' );

		/** @var AbstractWrapperTest_Subject|PHPUnit_Framework_MockObject_MockObject $mock */
		$mock = $builder->getMock();
		$mock->expects( $this->any() )
		     ->method( 'getAllFileNames' )
		     ->willReturn( [ $failedFilePath ] );

		foreach ( $mock->getDataTree() as $item ) {
			// just a loop to activate the generator.
		};

		$this->assertNotEmpty( $mock->getErrors() );
		$this->assertArrayHasKey( $failedFilePath, $mock->getErrors() );

		/** @var Error $first */
		$all = $mock->getErrors();
		$this->assertInstanceOf( '\\PhpParser\\Error', $all[ $failedFilePath ][0] );

		$this->assertContains( "unexpected '<'", $all[ $failedFilePath ][0]->getMessage() );
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testItThrowsExceptionIfBaseIsNotGiven() {
		new AbstractWrapperTest_Subject( null );
	}
}

class AbstractWrapperTest_Subject extends AbstractWrapper {
	public function getBasePath() {
		return $this->getBase();
	}

	protected function fetchFileNames() {
		$this->fileNames = [ ];
	}
}