<?php

namespace Test\PHPSemVer\Constraints;


use PhpParser\Node\Stmt\Function_;
use PHPSemVer\Constraints\FailedConstraint;

class FailedConstraintTest extends \PHPUnit_Framework_TestCase
{
    public function testItContainsTheSubject()
    {
        $failedConstraint = new FailedConstraint();

        $value = new Function_('foo');
        $failedConstraint->setValue($value);

        $this->assertEquals($value, $failedConstraint->getValue());
    }

    public function testItContainsTheOther()
    {
        $failedConstraint = new FailedConstraint();

        $value = new Function_('other');
        $failedConstraint->setOther($value);

        $this->assertEquals($value, $failedConstraint->getOther());
    }

}
