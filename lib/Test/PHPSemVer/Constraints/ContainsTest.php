<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 03.12.15
 * Time: 00:24
 */

namespace Test\PHPSemVer\Constraints;


use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Const_;
use PhpParser\Node\Stmt\Function_;
use PHPSemVer\Constraints\Contains;
use Test\Abstract_TestCase;

class ContainsTest extends Abstract_TestCase
{
    /**
     * @expectedException \PHPSemVer\Constraints\FailedConstraint
     */
    public function itThrowsErrorIfNothingFound()
    {
        $needle = new ClassMethod(new Name(uniqid('y_')));
        $other  = new Class_(
            new Name('foo'),
            [
                'stmts' => [
                    new ClassMethod(new Name(uniqid('m_'))),
                    new ClassMethod(new Name(uniqid('m_'))),
                    new Function_(new Name('foo')),
                    new ClassMethod(new Name(uniqid('m_'))),
                ],
            ]
        );

        $constraint = new Contains($needle);

        $constraint->evaluate($other);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function itThrowsInvalidArgumentForUnsupportedTypes()
    {
        $constraint = new Contains(new Function_(new Name('foo')));
        $constraint->evaluate(null);
    }

    public function testItCanFindMethodsInClasses()
    {
        $needle = new ClassMethod(new Name(uniqid('p_')));
        $other  = new Class_(
            new Name('foo'),
            [
                'stmts' => [
                    new ClassMethod(new Name(uniqid('m_'))),
                    new ClassMethod(new Name(uniqid('m_'))),
                    new Function_(new Name('foo')),
                    $needle,
                    new ClassMethod(new Name(uniqid('m_'))),
                ],
            ]
        );

        $constraint = new Contains($needle);

        $this->assertTrue($constraint->evaluate($other));
    }
}