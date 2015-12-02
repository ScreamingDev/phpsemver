<?php

define( 'PHPSEMVER_NAME', 'PHPSemVer' );
define( 'PHPSEMVER_ID', 'phpsemver' );
define( 'PHPSEMVER_PHP_BIN_PATH', getenv( 'PHP_PEAR_PHP_BIN' )
	?: '/usr/bin/env php' );
define( 'PHPSEMVER_BIN_PATH', __FILE__ );
define( 'PHPSEMVER_BASE_PATH', dirname( __DIR__ ) );
define( 'PHPSEMVER_LIB_PATH', PHPSEMVER_BASE_PATH . DIRECTORY_SEPARATOR
                              . 'lib' );
define( 'PHPSEMVER_TEXTDOMAIN', PHPSEMVER_ID );
define( 'PHPSEMVER_VERSION', '0.1.0' );

if ( ! is_readable( PHPSEMVER_LIB_PATH ) ) {
	die(
		'Something went terribly wrong'
		. PHP_EOL . 'because I was to dumb to think of such situation!'
		. PHP_EOL . 'Please blame me for that in the issue tracker ;)'
	);
}


// find composer file
function phpsemverLoader() {
	$projectAutoloadFile = PHPSEMVER_BASE_PATH . '/../../autoload.php';
	if (file_exists($projectAutoloadFile)) {
		return (require_once $projectAutoloadFile);
	}

	return (require_once dirname(__DIR__) . '/vendor/autoload.php');
}

$loader = phpsemverLoader();

if (!$loader) {
	die(
		'You must set up the project dependencies, run the following commands:'
		. PHP_EOL .
		'curl -s http://getcomposer.org/installer | php' . PHP_EOL .
		'php composer.phar install' . PHP_EOL
	);
}
