<?php

namespace Test\PHPSemVer\Trigger\Classes;


use PhpParser\Node\Stmt\Class_;
use PHPSemVer\Trigger\Classes\IsRemoved;

class IsRemovedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox ::handle is null, when argument is not in domain.
     */
    public function testHandleNullWhenInvalidArgument()
    {
        $test = new IsRemoved();

        $this->assertNull($test->handle(null, [], []));
    }

    public function testItContainsErrorMessageWhenSubjectNotFound()
    {
        $test = new IsRemoved();

        $func                 = new Class_('some_class');
        $func->namespacedName = $func->name;

        $old = [$func];
        $new = [];

        $this->assertTrue(
            $test->handle($func, $old, $new)
        );

        $this->assertTrue($test->isTriggered());

        $this->assertInstanceOf(
            'PHPSemVer\Constraints\FailedConstraint',
            $test->lastException
        );

        $this->assertEquals(
            'some_class removed.',
            $test->lastException->getMessage()
        );
    }

    public function testItHandlesFunctions()
    {
        $test = new IsRemoved();

        $this->assertTrue($test->canHandle(new Class_('some_class ')));
    }

    public function testItIsFalseWhenSubjectStillThere()
    {
        $test = new IsRemoved();

        $func = new Class_('some_class');

        $old = [$func];
        $new = [$func];

        $this->assertFalse($test->handle($func, $old, $new));
    }
}
