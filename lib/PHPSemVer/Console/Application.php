<?php
/**
 * Console application.
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * https://opensource.org/licenses/MIT. If you did not receive a copy
 * of the PHP License and are unable to obtain it through the web, please send
 * a note to pretzlaw@gmail.com so we can mail you a copy immediately.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */

namespace PHPSemVer\Console;

/**
 * Console application.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class Application extends \Symfony\Component\Console\Application
{
    public function __construct()
    {
        parent::__construct( PHPSEMVER_NAME, PHPSEMVER_VERSION );

        $this->fetchCommands();

        $definition = $this->getDefinition();

        $this->setDefinition( $definition );
    }

    public function fetchCommands()
    {
        $Directory = new \RecursiveDirectoryIterator(__DIR__);
        $Iterator  = new \RecursiveIteratorIterator($Directory);

        // try to find all commands
        $Regex = new \RegexIterator(
            $Iterator,
            '@.*Command.php@',
            \RecursiveRegexIterator::GET_MATCH
        );

        // add if is command
        foreach ($Regex as $single) {
            $file = preg_replace(
                '@' . preg_quote(PHPSEMVER_LIB_PATH, '@') . '@',
                '',
                $single[0],
                1
            );


            // skip abstracts
            if (false !== strpos($file, '/Abstract')) {
                continue;
            }

            $class = str_replace(DIRECTORY_SEPARATOR, '\\', dirname($file))
                     . '\\' . basename($file, '.php');

            // skip false positives
            if (!class_exists($class)) {
                continue;
            }

            $this->add(new $class);
        }
    }
}