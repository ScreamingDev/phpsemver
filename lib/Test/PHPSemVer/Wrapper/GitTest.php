<?php

namespace Test\PHPSemVer\Wrapper;

use GitWrapper\GitCommand;
use PHPSemVer\Wrapper\Directory;
use PHPSemVer\Wrapper\Git;
use Symfony\Component\Filesystem\Filesystem;
use Test\Abstract_TestCase;


class GitTest extends Abstract_TestCase
{
    public function dataFileList()
    {
        return array(
            array(
                'This is the content! Hello :)'
            )
        );
    }

    public function getGitLsFilesOutputData()
    {
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

    public function testItCanGenerateATemporaryPath()
    {
        /** @var Git $git */
        $git = $this->getTargetInstance( 'HEAD' );

        $this->assertContains( sys_get_temp_dir(), $git->getTempPath() );
    }

    public function testItCreatesATemporaryPath()
    {
        // Given the directory does not exists
        $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid( PHPSEMVER_ID );
        $this->assertFalse( is_dir( $tempDir ) );

        // When I stub the class
        $mockBuilder = $this->getTargetMockBuilder();

        $stub = $mockBuilder->setMethods( array( 'getTempPath' ) )->getMock();

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

    /**
     * @param $data
     *
     * @dataProvider getGitLsFilesOutputData
     */
    public function testItProvidesAllFileNames( $data )
    {
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

        // assert empty dir
        $fs = new Filesystem();
        $fs->remove($git->getTempPath());

        // create directory
        mkdir( $git->getTempPath(), 0777, true );

        // set wrapper for temporary path
        $reflectObject = new \ReflectionObject( $git );
        $property      = $reflectObject->getProperty( '_fileWrapper' );
        $property->setAccessible( true );
        $property->setValue( $git, new Directory( $git->getTempPath() ) );

        $this->assertEquals( $data, array_keys( $git->getAllFileNames() ) );

    }

    public function testItSupportsTheTemporaryPathForAFile()
    {
        $git = $this->getTargetInstance( 'HEAD' );

        $fileName = 'somefile.txt';

        $filePath = $git->getPath( $fileName );

        $this->assertFileExists( $filePath );
        $this->assertContains( $fileName, $filePath );
    }

    public function testItUsesAGitWrapper()
    {
        $git = $this->getTargetInstance( 'HEAD' );

        $reflectObject = new \ReflectionObject( $git );

        $method = $reflectObject->getMethod( '_getGitWrapper' );
        $method->setAccessible( true );

        $this->assertInstanceOf(
            '\\GitWrapper\\GitWrapper',
            $method->invoke( $git )
        );
    }

    public function testItWillCreateTemporaryPathsIfNoneExists()
    {
        /** @var Git $git */
        $git = $this->getTargetInstance( 'HEAD' );

        $targetFile = 'foo/bar/' . uniqid();
        $target     = $git->getBasePath() . $targetFile;

        $this->assertFileNotExists( $target );

        $fs = new Filesystem();
        $fs->remove(dirname( $target ));

        $this->assertFalse( is_dir( dirname( $target ) ) );

        $path = $git->getPath( $targetFile );

        $this->assertSame( $target, $path );
        $this->assertFileExists( $target );
        $this->assertTrue( is_dir( dirname( $target ) ) );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessagen Could not resolve ref xyz
     */
    public function testItThrowsExceptionWhenBaseIsInvalid()
    {
        new Git('xyz');
    }
}
