<?php

define('SEMVER_PHP_BIN_PATH', getenv('PHP_PEAR_PHP_BIN')
	?: '/usr/bin/env php');
define('SEMVER_BIN_PATH', __FILE__);
define(
'SEMVER_LIB_PATH',
	dirname(__DIR__) . DIRECTORY_SEPARATOR . 'lib'
);
define('SEMVER_TEXTDOMAIN', 'semver');
define('SEMVER_VERSION', 'DEV');

if (!is_readable(SEMVER_LIB_PATH)) {
	die(
		'Something went terribly wrong'
		. PHP_EOL . 'because I was to dumb to think of such situation!'
		. PHP_EOL . 'Please blame me for that in the issue tracker ;)'
	);
}


// find composer file
function semver_get_composer_config($dir)
{
	$composer_config = false;

	do {
		$composer_json_file = $dir . DIRECTORY_SEPARATOR . 'composer.json';

		if (is_readable($composer_json_file)) {
			$composer_json = json_decode(
				file_get_contents($composer_json_file),
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
			if (isset($composer_json['config'])) {
				$composer_config = array_merge(
					$composer_config,
					$composer_json['config']
				);
			}

			break;
		}

		$dir = dirname($dir);
	} while ($dir != dirname($dir));

	return $composer_config;
}

$composerConfig = semver_get_composer_config(SEMVER_BIN_PATH);

$vendorDir = $composerConfig['_base-dir']
			 . DIRECTORY_SEPARATOR . $composerConfig['vendor-dir'];

$loaderPath = $vendorDir . DIRECTORY_SEPARATOR . 'autoload.php';

if (!is_readable($loaderPath)) {
	die(
		'You must set up the project dependencies, run the following commands:'
		. PHP_EOL .
		'curl -s http://getcomposer.org/installer | php' . PHP_EOL .
		'php composer.phar install' . PHP_EOL
	);
}

$loader = require $loaderPath;

$loader = new \Symfony\Component\ClassLoader\ClassLoader();
$loader->addPrefix('Semver', SEMVER_LIB_PATH);
$loader->addPrefix(false, SEMVER_LIB_PATH);
$loader->register();