<?php

namespace Test\PHPSemVer\Trigger\Classes\Methods;


use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPSemVer\Trigger\Classes\Methods\ReturnTypeChanged;

class ReturnTypeChangedTest extends \PHPUnit_Framework_TestCase
{
    public function testItReturnNullOnInvalidSubject()
    {
        $test = new ReturnTypeChanged();
        $this->assertNull($test->handle(null, new ClassMethod('foo')));
        $this->assertNull($test->handle(new ClassMethod('foo'), null));
    }

    public function testItReturnFalseWhenNothingChanged()
    {
        $test = new ReturnTypeChanged();

        $old = new ClassMethod('foo');
        $old->returnType = 'bar';

        $this->assertFalse($test->handle($old, $old));
    }

    public function testItReturnTrueWhenReturnTypeChanged()
    {
        $test = new ReturnTypeChanged();

        $old = new ClassMethod('foo');
        $old->returnType = 'void if seal is broken';

        $class = new Class_('the_class');
        $class->namespacedName = $class->name;

        $old->setAttribute('parent', $class);

        $this->assertTrue($test->handle($old, new ClassMethod('foo')));
    }

    public function testItContainsAMessageWhenReturnTypeChanged()
    {
        $test = new ReturnTypeChanged();

        $old = new ClassMethod('foo');
        $old->returnType = 'void if seal is broken';

        $class = new Class_('the_class');
        $class->namespacedName = $class->name;

        $old->setAttribute('parent', $class);

        $test->handle($old, new ClassMethod('foo'));

        $this->assertContains('changed return type', $test->lastException->getMessage());
    }
}
