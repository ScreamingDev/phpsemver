<?php

namespace Test;


class Abstract_TestCase extends \PHPUnit_Framework_TestCase
{
    const BASE_DIR = __DIR__;

    public function getTargetInstance()
    {
        $reflect = new \ReflectionClass( $this->getTargetClass() );

        return call_user_func_array(
            array( $reflect, 'newInstance' ),
            func_get_args()
        );
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

    public function setProperty( $object, $propertyName, $value )
    {
        $reflection = new \ReflectionObject( $object );
        $property   = $reflection->getProperty( $propertyName );
        $property->setAccessible( true );

        $property->setValue( $object, $value );
    }
}