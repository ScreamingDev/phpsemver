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

use GitWrapper\GitException;
use GitWrapper\GitWrapper;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Wrapper for GIT context.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class Git extends AbstractWrapper
{

    protected $_fileWrapper;
    protected $_gitWrapper;
    protected $_tempPath;

    public function __construct($base)
    {
        parent::__construct($base);

        if ( ! is_dir($this->getTempPath())) {
            mkdir($this->getTempPath(), 0777, true);
        }

        $this->_fileWrapper = new Directory($this->getTempPath());
    }

    public function getTempPath()
    {
        if ( ! $this->_tempPath) {
            $this->_tempPath = sys_get_temp_dir()
                . DIRECTORY_SEPARATOR . uniqid(PHPSEMVER_ID);
        }

        return $this->_tempPath;
    }

    public function getAllFileNames()
    {

        $options = array(
            'with-tree' => $this->getBase(),
        );

        $git = $this->_getGitWrapper()->workingCopy(getcwd());

        $result = $git->run(
            array(
                'ls-files',
                $options
            )
        );

        $allPrevious = explode(PHP_EOL, $result->getOutput());
        $allPrevious = array_filter($allPrevious);

        $allFileNames = array();
        foreach ($allPrevious as $singleFile) {
            $allFileNames[$singleFile] = $this->getPath($singleFile);
        }

        return $allFileNames;
    }

    /**
     * Get internal wrapper.
     *
     * @return GitWrapper
     */
    protected function _getGitWrapper()
    {
        if ( ! $this->_gitWrapper) {
            $this->_gitWrapper = new GitWrapper();
        }

        return $this->_gitWrapper;
    }

    public function getPath($fileName)
    {
        $fullName = $this->_getFileWrapper()->getPath($fileName);

        if ( ! file_exists($fullName)) {
            $dir = dirname($fullName);
            if ( ! is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            file_put_contents($fullName, '');

            // last state but suppress error messages
            $gitWrapper = $this->_getGitWrapper();
            $git        = $gitWrapper->workingCopy(getcwd());

            try {
                $git->run(
                    array(
                        'show',
                        $this->getBase() . ':' . $fileName
                    )
                );


                $content = $git->getOutput();
            } catch (GitException $e) {
                $content = '';
            }

            file_put_contents($fullName, $content);
        }

        return $fullName;
    }

    /**
     * Get internal wrapper for files.
     *
     * @return Directory
     */
    protected function _getFileWrapper()
    {
        return $this->_fileWrapper;
    }

    function __destruct()
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove($this->getTempPath());
    }

    public function getBasePath()
    {
        return $this->_getFileWrapper()->getBasePath();
    }
}