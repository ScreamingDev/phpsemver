<?php

namespace Test\PHPSemVer\Wrapper;


class GitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param null $constructorArgs
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getTargetMock( $constructorArgs = null )
    {
        $mockBuilder = $this->getTargetMockBuilder( $constructorArgs );

        return $mockBuilder->getMock();
    }

    /**
     * @param null $constructorArgs
     *
     * @return \PHPUnit_Framework_MockObject_MockBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getTargetMockBuilder( $constructorArgs = null )
    {
        $mockBuilder = $this->getMockBuilder( $this->getTargetClass() );

        if ( null === $constructorArgs )
        {
            $mockBuilder->disableOriginalConstructor();

            return $mockBuilder;
        }

        $mockBuilder->setConstructorArgs( (array) $constructorArgs );

        return $mockBuilder;
    }

    /**
     * @return string
     */
    public function getTargetClass()
    {
        $className = preg_replace( '/^Test\\\\/', '', get_class( $this ), 1 );
        $className = preg_replace( '/Test$/', '', $className, 1 );

        return (string) $className;
    }

    public function testItCreatesATemporaryPath()
    {
        // Given the directory does not exists
        $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid( PHPSEMVER_ID );
        $this->assertFalse( is_dir( $tempDir ) );

        // When I stub the class
        $mockBuilder = $this->getTargetMockBuilder();
        $stub        = $mockBuilder->setMethods( array( 'getTempPath' ) )->getMock();

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
}
