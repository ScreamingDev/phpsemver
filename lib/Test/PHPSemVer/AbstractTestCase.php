<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 20.01.15
 * Time: 21:06
 */

namespace Test\PHPSemVer;


use PHPSemVer\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class AbstractTestCase extends \PHPUnit_Framework_TestCase
{
    public function getCommandTester($command)
    {
        $application = $this->getApplication();
        $command     = $application->find($command);

        return new CommandTester($command);
    }

    public function getApplication()
    {
        return new Application();
    }
}