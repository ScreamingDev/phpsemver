<?php

namespace Test\PHPSemVer\Console;


use PHPSemVer\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CompareCommandTest extends \PHPUnit_Framework_TestCase {
	public function testDispatchesErrorMessageWhenWrapperDoesNotExists() {
		$application = new Application();

		$command       = $application->find( 'compare' );
		$commandTester = new CommandTester( $command );
		$commandTester->execute(
			array(
				'command' => $command->getName(),
				'--type' => 'foo',
				'HEAD^1'
			)
		);

		$this->assertContains(
			'Unknown wrapper',
			$commandTester->getDisplay()
		);
	}
}