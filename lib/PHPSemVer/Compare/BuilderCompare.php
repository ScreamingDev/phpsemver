<?php
/**
 * Contains parser.
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

namespace PHPSemVer\Compare;

use PDepend\Source\AST\ASTArtifactList;
use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\Constraint\MajorException;
use PHPSemVer\Constraint\MinorException;
use PHPSemVer\Constraint\NamespaceExists;
use PHPSemVer\Constraint\PatchException;

/**
 * Compare two build / versions.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 *
 * @deprecated 3.0.0
 */
class BuilderCompare
{
    const OUTPUT_GENERAL = 'general';
    const OUTPUT_MAJOR   = 'major';
    const OUTPUT_MINOR   = 'minor';
    const OUTPUT_PATCH   = 'patch';
    protected $_newNamespaces = array();
    protected $_oldNamespaces = array();
    protected $_output;
    protected $latest;
    protected $previous;

    public function __construct(PHPBuilder $previous, PHPBuilder $latest)
    {
        $this->previous = $previous;

        $this->latest = $latest;
    }

    public function parse()
    {
        $this->testNamespaces();

    }

    public function testNamespaces()
    {
        foreach ($this->getPrevious()->getNamespaces() as $ast) {
            try {
                $this->assertNamespaceExists(
                    $ast->getName(),
                    $this->getLatest()
                );
            } catch (MajorException $e) {
                $this->appendException($e);
            }
        }
    }

    /**
     * Get previous version.
     *
     * @return PHPBuilder
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    public function assertNamespaceExists($namespace, $ast)
    {
        $assert = new NamespaceExists($namespace, $ast);

        $assert->run();
    }

    /**
     * Get latest version.
     *
     * @return PHPBuilder
     */
    public function getLatest()
    {
        return $this->latest;
    }

    /**
     * Add error message.
     *
     * @param \Exception $exception
     *
     * @return null
     */
    public function appendException($exception)
    {
        if ($exception instanceof MajorException) {
            $this->appendOutput($exception->getMessage(), static::OUTPUT_MAJOR);

            return null;
        }

        if ($exception instanceof MinorException) {
            $this->appendOutput($exception->getMessage(), static::OUTPUT_MINOR);

            return null;
        }

        if ($exception instanceof PatchException) {
            $this->appendOutput($exception->getMessage(), static::OUTPUT_PATCH);

            return null;
        }

        $this->appendOutput($exception->getMessage());
    }

    public function appendOutput($message, $type = self::OUTPUT_GENERAL)
    {
        $this->_output[$type][] = $message;
    }


}