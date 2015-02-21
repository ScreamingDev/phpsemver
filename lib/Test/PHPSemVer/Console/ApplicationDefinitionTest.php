<?php

namespace Test\PHPSemVer\Console;

use PHPSemVer\Console\Application;

class ApplicationDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testHasOptionForTargetDirectory()
    {
        $application = new Application();

        $this->assertInstanceOf(
            'Symfony\\Component\\Console\\Input\\InputOption',
            $application->getDefinition()->getOption( 'target' )
        );
    }
}