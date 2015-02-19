<?php

namespace Tests\Softec\Console;

use Softec\Console\Application;

class ApplicationDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testHasOptionForPrdPath()
    {
        $application = new Application();

        $this->assertInstanceOf(
            'Symfony\\Component\\Console\\Input\\InputOption',
            $application->getDefinition()->getOption('prd-path')
        );
    }
}