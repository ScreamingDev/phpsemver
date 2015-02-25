<?php

namespace Test\PHPSemVer\Console;

use PHPSemVer\Console\Application;

class ApplicationDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testHasTheCorrectVersion()
    {
        $application = new Application();

        $this->assertEquals( PHPSEMVER_VERSION, $application->getVersion() );
    }
}