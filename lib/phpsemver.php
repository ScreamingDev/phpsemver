<?php
/**
 * PHPSemVer.
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * https://opensource.org/licenses/MIT. If you did not receive a copy
 * of the PHP License and are unable to obtain it through the web, please send
 * a note to pretzlaw@gmail.com so we can mail you a copy immediately.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.1.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */

if (!ini_get('date.timezone')) {
    ini_set('date.timezone', 'UTC');
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';

$softecApplication = new \PHPSemVer\Console\Application();
$softecApplication->fetchCommands();

$softecApplication->run();