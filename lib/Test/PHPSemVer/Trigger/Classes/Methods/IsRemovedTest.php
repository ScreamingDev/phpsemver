<?php

namespace Test\PHPSemVer\Trigger\Classes\Methods;


use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPSemVer\Trigger\Classes\Methods\IsRemoved;

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

        $func                 = new ClassMethod('some_method');

        $class = new Class_('the_class');
        $class->namespacedName = $class->name;

        $func->setAttribute('parent', $class);

        $old = $func;
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
            'the_class::some_method() removed.',
            $test->lastException->getMessage()
        );
    }

    public function testItHandlesFunctions()
    {
        $test = new IsRemoved();

        $this->assertTrue($test->canHandle(new ClassMethod('some_method ')));
    }

    public function testItIsFalseWhenSubjectStillThere()
    {
        $test = new IsRemoved();

        $func = new ClassMethod('some_method');

        $old = $func;
        $new = $func;

        $this->assertFalse($test->handle($old, $new));
    }
}
