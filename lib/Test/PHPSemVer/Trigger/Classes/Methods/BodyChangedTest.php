<?php

namespace Test\PHPSemVer\Trigger\Classes\Methods;


use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPSemVer\Trigger\Classes\Methods\BodyChanged;

class BodyChangedTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleNullWhenInvalidArgument()
    {
        $test = new BodyChanged();

        $this->assertNull($test->handle(null, null));
        $this->assertNull($test->handle(new Function_('foo'), null));
        $this->assertNull($test->handle(null, new Function_('foo')));
    }

    public function testItContainsErrorMessageWhenBodyChanged()
    {
        $test = new BodyChanged();

        $func                 = new ClassMethod('some_method');

        $class = new Class_('the_class');
        $class->namespacedName = $class->name;

        $func->setAttribute('parent', $class);

        $old        = $func;
        $new        = clone $func;
        $new->stmts = [1];

        $this->assertTrue($test->handle($old, $new));

        $this->assertTrue($test->isTriggered());

        $this->assertInstanceOf(
            'PHPSemVer\Constraints\FailedConstraint',
            $test->lastException
        );

        $this->assertEquals(
            'the_class::some_method() body changed',
            $test->lastException->getMessage()
        );
    }

    public function testItHandlesClassMethods()
    {
        $test = new BodyChanged();

        $this->assertTrue($test->canHandle(new ClassMethod('some_func ')));
    }

    public function testItIsFalseWhenNothingChanged()
    {
        $test = new BodyChanged();

        $func                 = new ClassMethod('some_method');

        $class = new Class_('the_class');
        $class->namespacedName = $class->name;

        $func->setAttribute('parent', $class);

        $old = $func;
        $new = $func;

        $this->assertFalse($test->handle($func, $old, $new));
    }
}
