<?php
/**
 * Bootstrap for PHPSemVer.
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * https://opensource.org/licenses/MIT. If you did not receive a copy
 * of the PHP License and are unable to obtain it through the web, please send
 * a note to pretzlaw@gmail.com so we can mail you a copy immediately.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.2.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */

define( 'PHPSEMVER_NAME', 'PHPSemVer' );
define( 'PHPSEMVER_ID', 'phpsemver' );
define( 'PHPSEMVER_PHP_BIN_PATH', getenv( 'PHP_PEAR_PHP_BIN' )
	?: '/usr/bin/env php' );
define( 'PHPSEMVER_BIN_PATH', __FILE__ );
define( 'PHPSEMVER_BASE_PATH', dirname( __DIR__ ) );
define( 'PHPSEMVER_LIB_PATH', PHPSEMVER_BASE_PATH . DIRECTORY_SEPARATOR
                              . 'lib' );
define( 'PHPSEMVER_TEXTDOMAIN', PHPSEMVER_ID );
define( 'PHPSEMVER_VERSION', '3.2.0' );

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

	return (require_once PHPSEMVER_BASE_PATH . '/vendor/autoload.php');
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
