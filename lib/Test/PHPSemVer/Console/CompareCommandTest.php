<?php

namespace Test\PHPSemVer\Console;


use PHPSemVer\Console\Application;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

function testFunction() {
    return 'to see what the parser does.';
}

class CompareCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown wrapper-type "foo"
     */
    public function testDispatchesErrorMessageWhenWrapperDoesNotExists()
    {
        $application = new Application();

        $command       = $application->find( 'compare' );
        $commandTester = new CommandTester( $command );
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

        $argument = $command->getDefinition()->getOption( 'type' );

        $this->assertNotEquals(
            $argument->getDefault(),
            $commandTester->getInput()->getOption( 'type' )
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
}