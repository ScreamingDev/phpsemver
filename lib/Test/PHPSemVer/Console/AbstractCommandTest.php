<?php

namespace Test\PHPSemVer\Console;


use PHPSemVer\Console\AbstractCommand;
use PHPSemVer\Console\Application;

class AbstractCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testContainsApplication()
    {
        $subject = new AbstractCommandTest_Subject();
        $app = new Application();
        $app->addCommands([$subject]);

        $this->assertNotNull($subject->getApplication());
        $this->assertInstanceOf('PHPSemVer\\Console\\Application', $subject->getApplication());
    }

    public function testDebugMessagesCanBeFormatted()
    {
        $subject = new AbstractCommandTest_Subject();

        $outputMock = $this->getMock(
            'Symfony\Component\Console\Output\NullOutput',
            [
                'writeln',
                'isDebug'
            ]
        );

        $outputMock->expects($this->any())
                   ->method('isDebug')
                   ->willReturn(true);

        $outputMock->expects($this->once())
                   ->method('writeln')
                   ->with($this->equalTo('1 well formatted'));

        $subject->setOutput($outputMock);

        $subject->debug('%d %s %s', 1, 'well', 'formatted');
    }
}

class AbstractCommandTest_Subject extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('phpunit:phpsemver:console:abstractcommandtest_subject');
    }

}
