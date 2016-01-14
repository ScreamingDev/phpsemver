<?php

namespace Test\PHPSemVer\Trigger\Interfaces;


use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PHPSemVer\Trigger\Interfaces\IsAdded;

class IsAddedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox ::handle is null, when argument is not in domain.
     */
    public function testHandleNullWhenInvalidArgument()
    {
        $test = new IsAdded();

        $this->assertNull($test->handle(null, null));
    }

    public function testItContainsErrorMessageWhenSubjectFound()
    {
        $test = new IsAdded();

        $interface                 = new Interface_('some_interface');
        $interface->namespacedName = $interface->name;

        $old = null;
        $new = $interface;

        $this->assertTrue($test->handle($old, $new));

        $this->assertTrue($test->isTriggered());

        $this->assertInstanceOf(
            'PHPSemVer\Constraints\FailedConstraint',
            $test->lastException
        );

        $this->assertEquals(
            'some_interface added',
            $test->lastException->getMessage()
        );
    }

    public function testItHandlesInterfaces()
    {
        $test = new IsAdded();

        $this->assertTrue($test->canHandle(new Interface_('some_interface')));
    }

    public function testItIsFalseWhenSubjectStillThere()
    {
        $test = new IsAdded();

        $interface = new Interface_('some_interface');

        $old = $interface;
        $new = $interface;

        $this->assertFalse($test->handle($interface, $old, $new));
    }
}
