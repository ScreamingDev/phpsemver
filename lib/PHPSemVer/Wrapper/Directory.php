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
 * @copyright 2015-2016 Mike Pretzlaw. All rights reserved.
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.2.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */

namespace PHPSemVer\Wrapper;


use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Wrapper for directories.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015-2016 Mike Pretzlaw. All rights reserved.
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.2.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class Directory extends AbstractWrapper
{
    function fetchFileNames()
    {
        $finder = new Finder();
        $finder->in($this->getBasePath())
               ->files()
               ->name('*.php');

        $this->fileNames = array();
        foreach ($finder as $single) {
            /* @var SplFileInfo $single */
            $this->fileNames[$single->getRelativePathname()] = $single->getRealPath();
        }
    }

    public function getBasePath()
    {
        return realpath($this->getBase()).DIRECTORY_SEPARATOR;
    }

    public function getPath($fileName)
    {
        return $this->getBasePath().ltrim($fileName, DIRECTORY_SEPARATOR);
    }
}