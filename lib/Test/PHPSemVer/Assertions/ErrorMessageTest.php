<?php

namespace Test\PHPSemVer\Assertions;


use PHPSemVer\Assertions\ErrorMessage;
use Test\Abstract_TestCase;

class ErrorMessageTest extends Abstract_TestCase
{
    public function testItShowsAnErrorMessage()
    {
        $err = new ErrorMessage( 'rule', 'message' );

        $this->assertEquals( 'message', $err->getMessage() );
    }

    public function testItShowsTheRule()
    {
        $err = new ErrorMessage( 'rule', 'message' );

        $this->assertEquals( 'rule', $err->getRule() );
    }
}
