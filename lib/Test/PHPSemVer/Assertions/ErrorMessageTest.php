<?php

namespace Test\PHPSemVer\Assertions;


use PHPSemVer\Assertions\Message;
use Test\Abstract_TestCase;

class ErrorMessageTest extends Abstract_TestCase
{
    public function testItShowsAnErrorMessage()
    {
        $err = new Message( 'rule', 'message' );

        $this->assertEquals( 'message', $err->getMessage() );
    }

    public function testItShowsTheRule()
    {
        $err = new Message( 'rule', 'message' );

        $this->assertEquals( 'rule', $err->getRule() );
    }
}
