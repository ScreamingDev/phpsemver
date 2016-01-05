<?php

namespace Test\PHPSemVer\Trigger\Functions;


use PhpParser\Node\Stmt\Function_;
use PHPSemVer\Trigger\Functions\BodyChanged;

class BodyChangedTest extends \PHPUnit_Framework_TestCase
{
    public function testDifferentClassesAreDifferent()
    {
        $test = new BodyChanged();

        $mock = $this->getMock(get_class($test), ['canHandle']);

        $mock->expects($this->any())
            ->method('canHandle')
            ->willReturn(true);

        $this->assertFalse($mock->handle([new \stdClass()], [new \ArrayObject()]));
    }

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

        $func                 = new Function_('some_func');
        $func->namespacedName = $func->name;

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
            'some_func() body changed.',
            $test->lastException->getMessage()
        );
    }

    public function testItHandlesFunctions()
    {
        $test = new BodyChanged();

        $this->assertTrue($test->canHandle(new Function_('some_func ')));
    }

    public function testItIsFalseWhenNothingChanged()
    {
        $test = new BodyChanged();

        $func = new Function_('some_func');

        $old = $func;
        $new = $func;

        $this->assertFalse($test->handle($func, $old, $new));
    }
}
