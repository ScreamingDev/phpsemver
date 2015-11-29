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
function phpsemver_get_composer_config( $dir ) {
	$composer_config = false;

	$final_composer_config = [];
	while ( dirname( $dir ) != $dir ) {
		$dir = dirname( $dir );

		$composer_json_file = $dir . DIRECTORY_SEPARATOR . 'composer.json';

		if ( ! is_readable( $composer_json_file ) ) {
			continue;
		}


		$composer_json = json_decode(
			file_get_contents( $composer_json_file ),
			true
		);

		// default values (with the dir where it was found)
		$composer_config = [
			'_base-dir'  => $dir,
			//"bin-dir" => "bin",
			"vendor-dir" => "vendor",
			//"cache-dir" => "var/composer"
		];

		// override with config if given
		if ( isset( $composer_json['config'] ) ) {
			$final_composer_config = array_merge(
				$composer_config,
				$composer_json['config']
			);
		}
	}

	return $final_composer_config;
}

$composerConfig = phpsemver_get_composer_config( PHPSEMVER_BIN_PATH );

$vendorDir = $composerConfig['_base-dir']
             . DIRECTORY_SEPARATOR . $composerConfig['vendor-dir'];

$loaderPath = $vendorDir . DIRECTORY_SEPARATOR . 'autoload.php';

if ( ! file_exists( $loaderPath ) ) {
	die(
		'You must set up the project dependencies, run the following commands:'
		. PHP_EOL .
		'curl -s http://getcomposer.org/installer | php' . PHP_EOL .
		'php composer.phar install' . PHP_EOL
	);
}

$loader = require $loaderPath;

$loader = new \Symfony\Component\ClassLoader\ClassLoader();
$loader->addPrefix( 'PHPSemVer', PHPSEMVER_LIB_PATH );
$loader->addPrefix( false, PHPSEMVER_LIB_PATH );
$loader->register();