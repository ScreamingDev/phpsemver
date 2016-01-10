<?php


namespace PHPSemVer\Console\Vcs;


use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Test\Abstract_TestCase;

class MessageCommandTest extends Abstract_TestCase {
    public function testItListsChanges()
    {
        $application = new \PHPSemVer\Console\Application();

        $command       = $application->find( 'vcs:message' );

        $commandTester = new CommandTester( $command );
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                'previous' => $this->getResourcePath('v1'),
                'latest' => $this->getResourcePath('v2'),
            )
        );

        $this->assertContains('resource\RemovedClass::foo() added', $commandTester->getDisplay());
    }

    public function testThePrefixOfEachLineCanBeChanged()
    {
        $application = new \PHPSemVer\Console\Application();

        $command       = $application->find( 'vcs:message' );

        $prefix = uniqid('# ');

        $commandTester = new CommandTester( $command );
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                'previous' => $this->getResourcePath('v1'),
                'latest' => $this->getResourcePath('v2'),
                '--prefix' => $prefix,
            )
        );

        $this->assertContains(
            $prefix . 'resource\RemovedClass::foo() added',
            $commandTester->getDisplay()
        );
    }


    public function testItCanBeCutDownToOnlySomeRuleSets()
    {
        $application = new \PHPSemVer\Console\Application();

        $command       = $application->find( 'vcs:message' );

        $commandTester = new CommandTester( $command );
        $commandTester->execute(
            array(
                'command' => $command->getName(),
                'previous' => $this->getResourcePath('v1'),
                'latest' => $this->getResourcePath('v2'),
                '--include' => 'major'
            )
        );

        $this->assertNotContains('resource\RemovedClass::foo() added', $commandTester->getDisplay());
    }
}
