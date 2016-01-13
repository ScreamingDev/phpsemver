<?php

namespace Test\PHPSemVer\Trigger\Interfaces;


use PhpParser\Node\Stmt\Interface_;
use PHPSemVer\Trigger\Interfaces\IsRemoved;

class IsRemovedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox ::handle is null, when argument is not in domain.
     */
    public function testHandleNullWhenInvalidArgument()
    {
        $test = new IsRemoved();

        $this->assertNull($test->handle(null, null));
    }

    public function testItContainsErrorMessageWhenSubjectNotFound()
    {
        $test = new IsRemoved();

        $interface                 = new Interface_('some_interface');
        $interface->namespacedName = $interface->name;

        $old = $interface;
        $new = null;

        $this->assertTrue(
            $test->handle($old, $new)
        );

        $this->assertTrue($test->isTriggered());

        $this->assertInstanceOf(
            'PHPSemVer\Constraints\FailedConstraint',
            $test->lastException
        );

        $this->assertEquals(
            'some_interface removed',
            $test->lastException->getMessage()
        );
    }

    public function testItHandlesInterfaces()
    {
        $test = new IsRemoved();

        $this->assertTrue($test->canHandle(new Interface_('some_interface ')));
    }

    public function testItIsFalseWhenSubjectStillThere()
    {
        $test = new IsRemoved();

        $interface = new Interface_('some_interface');

        $old = $interface;
        $new = $interface;

        $this->assertFalse($test->handle($old, $new));
    }
}
