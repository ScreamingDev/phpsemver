<?php

namespace Test\PHPSemVer\Trigger\Classes\Methods;


use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPSemVer\Trigger\Classes\Methods\IsAdded;

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

        $func                 = new ClassMethod('some_method');

        $class = new Class_('the_class');
        $class->namespacedName = $class->name;

        $func->setAttribute('parent', $class);

        $old = null;
        $new = $func;

        $this->assertTrue($test->handle($old, $new));

        $this->assertTrue($test->isTriggered());

        $this->assertInstanceOf(
            'PHPSemVer\Constraints\FailedConstraint',
            $test->lastException
        );

        $this->assertEquals(
            'the_class::some_method() added.',
            $test->lastException->getMessage()
        );
    }

    public function testItHandlesFunctions()
    {
        $test = new IsAdded();

        $this->assertTrue($test->canHandle(new ClassMethod('some_method ')));
    }

    public function testItIsFalseWhenSubjectStillThere()
    {
        $test = new IsAdded();

        $func = new ClassMethod('some_method');

        $old = $func;
        $new = $func;

        $this->assertFalse($test->handle($func, $old, $new));
    }
}
