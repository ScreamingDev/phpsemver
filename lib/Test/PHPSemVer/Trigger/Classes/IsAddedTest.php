<?php

namespace Test\PHPSemVer\Trigger\Classes;


use PhpParser\Node\Stmt\Class_;
use PHPSemVer\Trigger\Classes\IsAdded;

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

        $func                 = new Class_('some_class');
        $func->namespacedName = $func->name;

        $old = null;
        $new = $func;

        $this->assertTrue($test->handle($old, $new));

        $this->assertTrue($test->isTriggered());

        $this->assertInstanceOf(
            'PHPSemVer\Constraints\FailedConstraint',
            $test->lastException
        );

        $this->assertEquals(
            'some_class added',
            $test->lastException->getMessage()
        );
    }

    public function testItHandlesFunctions()
    {
        $test = new IsAdded();

        $this->assertTrue($test->canHandle(new Class_('some_class ')));
    }

    public function testItIsFalseWhenSubjectStillThere()
    {
        $test = new IsAdded();

        $func = new Class_('some_class');

        $old = $func;
        $new = $func;

        $this->assertFalse($test->handle($func, $old, $new));
    }
}
