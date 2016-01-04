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
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.1.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */

namespace PHPSemVer\Console;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * Console application.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.1.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class Application extends \Symfony\Component\Console\Application
{
    public function __construct() {
        parent::__construct( PHPSEMVER_NAME, PHPSEMVER_VERSION );

        $this->fetchCommands();

        $definition = $this->getDefinition();

        $this->setDefinition( $definition );
    }

    public function fetchCommands()
    {
        $finder = new Finder();
        $finder->in( __DIR__ )->files()->name( '*Command.php' );

        foreach ( $finder as $commandFile ) {
            /* @var SplFileInfo $commandFile */

            $currentClass = strtr(
                $commandFile->getRealPath(),
                [
                    rtrim(PHPSEMVER_LIB_PATH, DIRECTORY_SEPARATOR) => '',
                    '/' => '\\',
                    $commandFile->getBasename() => $commandFile->getBasename('.php')
                ]
            );

            if ( ! class_exists($currentClass)) {
                continue;
            }

            if (false !== strpos($currentClass, '\\Abstract')) {
                continue;
            }


            $name = str_replace( __NAMESPACE__ . '\\', '', $currentClass );
            $name = preg_replace( '/Command$/', '', $name );
            $name = str_replace( '\\', ':', $name );
            $name = trim( $name, ':' );
            $name = strtolower( $name );

            /* @var AbstractCommand $command */
            $command = new $currentClass( $name );

            if (false == $command instanceof Command) {
                continue;
            }

            $reflectClass = new \ReflectionClass( $currentClass );
            $comment      = $reflectClass->getDocComment();
            preg_match( '/\s\*\s.*\./', $comment, $description );
            $description = preg_replace( '/^\s\*\s/', '', current( $description ) );

            $command->setDescription( $description );

            preg_match( '/(?<=\/\*\*).*(?=\n\s*\*\s@)/s', $comment, $help );      // get long description
            $help = preg_replace( '/\s*\*\s*/s', "\n", current( $help ) );          // remove comment-star
            $help = preg_replace( '/([^\n])\n{1}([^\n])/s', '$1 $2', $help );     // remove single new-lines
            $help = str_replace( "\n", "\n ", $help );                            // indent a bit

            $command->setHelp( $help );

            $this->add( $command );
        }
    }
}