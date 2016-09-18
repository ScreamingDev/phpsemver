<?php

namespace Test\PHPSemVer\Console;


use PhpParser\Error;
use PHPSemVer\Console\Application;
use PHPSemVer\Console\CompareCommand;
use PHPSemVer\Wrapper\Directory;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Test\Abstract_TestCase;

function testFunction()
{
    return 'to see what the parser does.';
}

class CompareCommandTest extends Abstract_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown wrapper-type "foo"
     */
    public function testDispatchesErrorMessageWhenWrapperDoesNotExists()
    {
        $application = new Application();

        $command = $application->find('compare');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                'previous' => 'HEAD~3',
                '--type' => 'foo',
            )
        );
    }

    public function testTheWrapperTypeCanBeChanged()
    {
        $application = new Application();

        $command       = $application->find( 'compare' );
        $commandTester = new CommandTester( $command );
        $commandTester->execute(
            array(
                'command'  => $command->getName(),
                '--type'   => 'directory',
                'previous' => __DIR__,
                'latest'   => __DIR__,
            ),
            [
                'verbosity' => OutputInterface::VERBOSITY_DEBUG
            ]
        );

        $argument = $command->getDefinition()->getOption('type');

        $this->assertNotEquals(
            $argument->getDefault(),
            $commandTester->getInput()->getOption('type')
        );

        $this->assertNotContains(
            'Unknown wrapper',
            $commandTester->getDisplay()
        );

        $this->assertContains(
            'Total time',
            $commandTester->getDisplay()
        );
    }

    public function testItCanShowErrors()
    {
        $application = new Application();

        $command = $application->find('compare');

        $showErrors = static::getMethod(CompareCommand::class, 'showErrors');

        // mock a wrapper with an error
        $previousErrors = [
            'foo_file.php' => new Error('foo_message'),
        ];

        $latestErrors = $previousErrors;
        $latestErrors['bar_file.php'] = new Error('bar_message');

        $wrapperWithError = $this->getMockBuilder(Directory::class)
            ->disableOriginalConstructor()
            ->setMethods(['getErrors'])
            ->getMock();

        $previousWrapperWithError = clone $wrapperWithError;

        $previousWrapperWithError->expects($this->any())
            ->method('getErrors')
            ->willReturn($previousErrors);


        $latestWrapperWithError = clone $wrapperWithError;

        $latestWrapperWithError->expects($this->any())
            ->method('getErrors')
            ->willReturn($latestErrors);

        $this->assertEquals($previousErrors, $previousWrapperWithError->getErrors());
        $this->assertEquals($latestErrors, $latestWrapperWithError->getErrors());

        $commandMockBuilder = $this->getMockBuilder(get_class($command))
            ->disableOriginalConstructor()
            ->setProxyTarget($command)
            ->setMethods(['getPreviousWrapper', 'getLatestWrapper']);

        $commandMock = $commandMockBuilder->getMock();
        $commandMock->expects($this->any())
            ->method('getPreviousWrapper')
            ->willReturn($previousWrapperWithError);

        $commandMock->expects($this->any())
            ->method('getLatestWrapper')
            ->willReturn($latestWrapperWithError);

        // mock output
        $output = new BufferedOutput();

        // run test
        $showErrors->invoke($commandMock, $output);

        $messages = $output->fetch();

        $this->assertContains('foo_file.php', $messages);
        $this->assertContains('bar_file.php', $messages);
    }
}