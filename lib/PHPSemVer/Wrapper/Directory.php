<?php
/**
 * Contains wrapper.
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

namespace PHPSemVer\Wrapper;


use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

/**
 * Wrapper for directories.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class Directory extends AbstractWrapper
{
    function getAllFileNames()
    {
        $Directory = new RecursiveDirectoryIterator($this->getBasePath());
        $Iterator  = new RecursiveIteratorIterator($Directory);
        $Regex     = new RegexIterator(
            $Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH
        );

        $allFileNames = array();
        foreach ($Regex as $single) {
            if ($this->getExcludePattern()) {
                foreach ($this->getExcludePattern() as $pattern) {
                    if (!$pattern) {
                        // skip empty pattern
                        continue;
                    }

                    if (false !== strpos($single[0], $pattern)) {
                        continue 2;
                    }

                }
            }

            $short = str_replace($this->getBasePath(), '', $single[0]);

            $allFileNames[$short] = $single[0];
        }

        return $allFileNames;
    }

    public function getBasePath()
    {
        if ( ! $this->getBase()) {
            return '';
        }

        return realpath($this->getBase()).DIRECTORY_SEPARATOR;
    }

    public function getPath($fileName)
    {
        return $this->getBasePath().ltrim($fileName, DIRECTORY_SEPARATOR);
    }
}