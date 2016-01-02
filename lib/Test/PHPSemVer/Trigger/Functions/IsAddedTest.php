<?php

namespace Test\PHPSemVer\Trigger\Functions;


use PhpParser\Node\Stmt\Function_;
use PHPSemVer\Trigger\Functions\IsAdded;

class IsAddedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @testdox ::handle is null, when argument is not in domain.
     */
    public function testHandleNullWhenInvalidArgument()
    {
        $test = new IsAdded();

        $this->assertNull($test->handle(null, [], []));
    }

    public function testItContainsErrorMessageWhenSubjectFound()
    {
        $test = new IsAdded();

        $func                 = new Function_('some_func');
        $func->namespacedName = $func->name;

        $old = [];
        $new = [$func];

        $this->assertTrue($test->handle($func, $old, $new));

        $this->assertTrue($test->isTriggered());

        $this->assertInstanceOf(
            'PHPSemVer\Constraints\FailedConstraint',
            $test->lastException
        );

        $this->assertEquals(
            'some_func() added.',
            $test->lastException->getMessage()
        );
    }

    public function testItHandlesFunctions()
    {
        $test = new IsAdded();

        $this->assertTrue($test->canHandle(new Function_('some_func ')));
    }

    public function testItIsFalseWhenSubjectStillThere()
    {
        $test = new IsAdded();

        $func = new Function_('some_func');

        $old = [$func];
        $new = [$func];

        $this->assertFalse($test->handle($func, $old, $new));
    }
}
