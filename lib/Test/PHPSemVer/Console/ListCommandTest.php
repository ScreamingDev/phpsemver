<?php

namespace Test\PHPSemVer\Console;


use PHPSemVer\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ListCommandTest extends \PHPUnit_Framework_TestCase {
	public function testCanListAllPossibleCommands() {
		$application = new Application();

		$command       = $application->find( 'list' );
		$commandTester = new CommandTester( $command );
		$commandTester->execute( array(  ) );

        $output = $commandTester->getDisplay();

        $this->assertRegExp( '/list\s*Lists/', $output);

        $this->assertContains('compare', $output);
        $this->assertContains('vcs:message', $output);
	}
}