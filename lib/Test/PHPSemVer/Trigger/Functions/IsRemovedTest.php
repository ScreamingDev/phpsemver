<?php

namespace Test\PHPSemVer\Trigger\Functions;


use PhpParser\Node\Stmt\Function_;
use PHPSemVer\Constraints\FailedConstraint;
use PHPSemVer\Trigger\Functions\IsRemoved;

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

        $func                 = new Function_('some_func');
        $func->namespacedName = $func->name;

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
            'some_func() removed.',
            $test->lastException->getMessage()
        );
    }

    public function testItHandlesFunctions()
    {
        $test = new IsRemoved();

        $this->assertTrue($test->canHandle(new Function_('some_func ')));
    }

    public function testItIsFalseWhenSubjectStillThere()
    {
        $test = new IsRemoved();

        $func = new Function_('some_func');

        $old = $func;
        $new = $func;

        $this->assertFalse($test->handle($old, $new));
    }
}
