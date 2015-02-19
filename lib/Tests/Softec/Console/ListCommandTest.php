<?php

namespace Tests\Softec\Console;


use Softec\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ListCommandTest extends \PHPUnit_Framework_TestCase {
	public function testCanListAllPossibleCommands() {
		$application = new Application();

		$command       = $application->find( 'list' );
		$commandTester = new CommandTester( $command );
		$commandTester->execute( array( 'command' => $command->getName() ) );

		$this->assertRegExp( '/list\s*Lists/', $commandTester->getDisplay() );
	}
}