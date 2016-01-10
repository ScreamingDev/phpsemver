<?php

namespace Test\PHPSemVer\Console;


use PHPSemVer\Console\AbstractCommand;
use PHPSemVer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Test\Abstract_TestCase;

class AbstractCommandTest extends Abstract_TestCase
{
    public function testContainsApplication()
    {
        $subject = new AbstractCommandTest_Subject();
        $app = new Application();
        $app->addCommands([$subject]);

        $this->assertNotNull($subject->getApplication());
        $this->assertInstanceOf('PHPSemVer\\Console\\Application', $subject->getApplication());
    }

    public function testItParsesTheConfiguration()
    {
        $subject = new AbstractCommandTest_Subject();

        $subject->setInput(
            new ArrayInput(
                [
                    '--ruleSet' => $this->getResourcePath('Rules/Empty.xml'),
                    'previous' => 'HEAD~1'
                ],
                $subject->getDefinition()
            )
        );

        $subject->setOutput(new NullOutput());

        $this->assertInstanceOf('PHPSemVer\\Config', $subject->getConfig());

        $this->assertNull($subject->getConfig()->ruleSet());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Could not find rule set: yomama
     */
    public function testItThrowsExceptionOnInvalidRuleSets()
    {
        $subject = new AbstractCommandTest_Subject();

        $subject->setInput(
            new ArrayInput(
                [
                    '--ruleSet' => 'yomama',
                    'previous' => 'HEAD~1'
                ],
                $subject->getDefinition()
            )
        );

        $subject->setOutput(new NullOutput());

        $subject->getConfig();
    }

    public function testItGeneratesTheEnvironment()
    {
        $subject = new AbstractCommandTest_Subject();

        $subject->setInput(
            new ArrayInput(
                [
                    '--ruleSet' => $this->getResourcePath('Rules/Empty.xml'),
                    'previous' => 'HEAD~1'
                ],
                $subject->getDefinition()
            )
        );

        $subject->setOutput(new NullOutput());

        $this->assertInstanceOf('PHPSemVer\\Environment', $subject->getEnvironment());
        $this->assertInstanceOf('PHPSemVer\\Config', $subject->getEnvironment()->getConfig());

        $this->assertNull($subject->getEnvironment()->getConfig()->ruleSet());
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
        parent::configure();

        $this->setName('phpunit:phpsemver:console:abstractcommandtest_subject');
    }

}
