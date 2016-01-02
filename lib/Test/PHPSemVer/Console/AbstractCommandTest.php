<?php

namespace Test\PHPSemVer\Console;


use PHPSemVer\Console\AbstractCommand;

class AbstractCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testSomething()
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
