<?php

namespace Test\PHPSemVer\Wrapper;


use GitWrapper\GitCommand;
use PHPSemVer\Wrapper\Git;
use Test\Abstract_TestCase;

class GitTest extends Abstract_TestCase {
	/**
	 * @param null $constructorArgs
	 *
	 * @return \PHPUnit_Framework_MockObject_MockBuilder|\PHPUnit_Framework_MockObject_MockObject
	 */
	public function getTargetMockBuilder( $constructorArgs = null ) {
		$mockBuilder = $this->getMockBuilder( $this->getTargetClass() );

		if ( null === $constructorArgs ) {
			$mockBuilder->disableOriginalConstructor();

			return $mockBuilder;
		}

		$mockBuilder->setConstructorArgs( (array) $constructorArgs );

		return $mockBuilder;
	}

	/**
	 * @return string
	 */
	public function getTargetClass() {
		$className = preg_replace( '/^Test\\\\/', '', get_class( $this ), 1 );
		$className = preg_replace( '/Test$/', '', $className, 1 );

		return (string) $className;
	}

	public function testItCreatesATemporaryPath() {
		// Given the directory does not exists
		$tempDir
			= sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid( PHPSEMVER_ID );
		$this->assertFalse( is_dir( $tempDir ) );

		// When I stub the class
		$mockBuilder = $this->getTargetMockBuilder();
		$stub        = $mockBuilder->setMethods( array( 'getTempPath' ) )
		                           ->getMock();

		// And mock the method "getTempDir"
		$stub->expects( $this->exactly( 4 ) )
		     ->method( 'getTempPath' )
		     ->willReturn( $tempDir );

		$this->assertEquals( $tempDir, $stub->getTempPath() );

		// And instantiate the class
		$reflectedClass = new \ReflectionClass( $this->getTargetClass() );
		$constructor    = $reflectedClass->getConstructor();
		$constructor->invoke( $stub, 'HEAD' );

		// Then the directory should be created
		$this->assertTrue( is_dir( $tempDir ) );
	}

	public function testItCanGenerateATemporaryPath() {
		/** @var Git $git */
		$git = $this->getTargetInstance( 'HEAD' );

		$this->assertContains( sys_get_temp_dir(), $git->getTempPath() );
	}

	/**
	 * @param $data
	 *
	 * @dataProvider getGitLsFilesOutputData
	 */
	public function testItProvidesAllFileNames( $data ) {
		$gitWrapper = $this->getMockBuilder( '\\GitWrapper\\GitWrapper' )
		                   ->disableOriginalConstructor()
		                   ->setMethods( array( 'run' ) )
		                   ->getMock();

		$terminalOutput = implode( PHP_EOL, $data );
		$gitWrapper->expects( $this->any() )
		           ->method( 'run' )
		           ->withAnyParameters()
		           ->willReturn( $terminalOutput );

		$command = GitCommand::getInstance();
		$command->setFlag( 'version' )
		        ->setDirectory( '.' );

		$this->assertSame( $terminalOutput, $gitWrapper->run( $command ) );

		$git = $this->getTargetMockBuilder()
		            ->disableOriginalConstructor()
		            ->setMethods( array( '_getGitWrapper' ) )
		            ->getMock();

		$git->expects( $this->any() )
		    ->method( '_getGitWrapper' )
		    ->willReturn( $gitWrapper );

		$this->assertEquals( $data, $git->getAllFileNames() );

//
//		var_dump($gitWrapper);

	}

	public function getGitLsFilesOutputData() {
		return array(
			array(
				array(
					'ONE.txt',
					'TWO.txt',
					'three.php',
					'four.xml'
				)
			)
		);
	}

	public function testItUsesAGitWrapper() {
		$git = $this->getTargetInstance( 'HEAD' );

		$reflectObject = new \ReflectionObject( $git );

		$method = $reflectObject->getMethod( '_getGitWrapper' );
		$method->setAccessible( true );

		$this->assertInstanceOf(
			'\\GitWrapper\\GitWrapper',
			$method->invoke( $git )
		);
	}

	public function testItSupportsTheTemporaryPathForAFile() {
		$git = $this->getTargetInstance( 'HEAD' );

		$fileName = 'somefile.txt';

		$filePath = $git->getPath( $fileName );

		$this->assertFileExists( $filePath );
		$this->assertContains( $fileName, $filePath );
	}

	/**
	 * @param $data
	 *
	 * @dataProvider dataFileList
	 */
	public function testTemporaryFilesContainContentsIfFound( $data ) {
		$workingCopy = $this->getMockBuilder( '\\GitWrapper\\GitWorkingCopy' )
		                    ->disableOriginalConstructor()
		                    ->setMethods( array( 'run', 'getOutput' ) )
		                    ->getMock();

		$workingCopy->expects( $this->any() )
		            ->method( 'getOutput' )
		            ->willReturn( $data );

		$workingCopy->expects( $this->any() )
		            ->method( 'run' )
		            ->willReturn( '' );

		$gitWrapper = $this->getMockBuilder( '\\GitWrapper\\GitWrapper' )
		                   ->disableOriginalConstructor()
		                   ->setMethods( array( 'workingCopy' ) )
		                   ->getMock();

		$gitWrapper->expects( $this->any() )
		           ->method( 'workingCopy' )
		           ->willReturn( $workingCopy );

		/** @var Git $git */
		$git = $this->getTargetMockBuilder( 'HEAD' )
		            ->setMethods( array( '_getGitWrapper' ) )
		            ->getMock();

		$git->expects( $this->any() )
		    ->method( '_getGitWrapper' )
		    ->willReturn( $gitWrapper );

		$fileName = 'somefile.txt';

		$filePath = $git->getPath( $fileName );

		$this->assertFileExists( $filePath );
		$this->assertContains( $fileName, $filePath );

		$this->assertSame( $data, file_get_contents( $filePath ) );
	}

	public function dataFileList() {
		return array(
			array(
				'This is the content! Hello :)'
			)
		);
	}

	public function testItWillCreateTemporaryPathsIfNoneExists() {
		/** @var Git $git */
		$git = $this->getTargetInstance( 'HEAD' );

		$targetFile = 'foo/bar/' . uniqid();
		$target     = $git->getBasePath() . $targetFile;

		$this->assertFileNotExists( $target );
		$this->assertFalse( is_dir( dirname( $target ) ) );

		$path = $git->getPath( $targetFile );

		$this->assertSame( $target, $path );
		$this->assertFileExists( $target );
		$this->assertTrue( is_dir( dirname( $target ) ) );
	}
}