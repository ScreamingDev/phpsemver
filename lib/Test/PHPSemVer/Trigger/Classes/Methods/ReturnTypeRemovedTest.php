<?php

namespace Test\PHPSemVer\Trigger\Classes\Methods;


use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPSemVer\Trigger\Classes\Methods\ReturnTypeRemoved;

class ReturnTypeRemovedTest extends \PHPUnit_Framework_TestCase
{
    public function testItContainsAMessageWhenReturnTypeRemoved()
    {
        $test = new ReturnTypeRemoved();

        $old             = new ClassMethod('foo');
        $old->returnType = 'void if seal is broken';

        $class                 = new Class_('the_class');
        $class->namespacedName = $class->name;

        $old->setAttribute('parent', $class);


        $new = new ClassMethod('foo');

        $this->assertTrue('' == (string) $new->getReturnType());

        $this->assertTrue($test->handle($old, $new));

        $this->assertContains('return type', $test->lastException->getMessage());
        $this->assertContains('were removed.', $test->lastException->getMessage());
    }

    public function testItReturnFalseWhenNothingChanged()
    {
        $test = new ReturnTypeRemoved();

        $old             = new ClassMethod('foo');
        $old->returnType = 'bar';

        $this->assertFalse($test->handle($old, $old));
    }

    public function testItReturnNullOnInvalidSubjects()
    {
        $test = new ReturnTypeRemoved();
        $this->assertNull($test->handle(null, new ClassMethod('foo')));
        $this->assertNull($test->handle(new ClassMethod('foo'), null));
    }

    public function testItReturnTrueWhenReturnTypeRemoved()
    {
        $test = new ReturnTypeRemoved();

        $old             = new ClassMethod('foo');
        $old->returnType = 'void if seal is broken';

        $class                 = new Class_('the_class');
        $class->namespacedName = $class->name;

        $old->setAttribute('parent', $class);

        $new = new ClassMethod('foo');

        $this->assertTrue('' == (string) $new->getReturnType());
        $this->assertTrue($test->handle($old, $new));
    }
}
