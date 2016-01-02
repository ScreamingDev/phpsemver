<?php
/**
 * Abstract wrapper.
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

use PhpParser\Error;
use PhpParser\Lexer\Emulative;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PHPSemVer\DataTree\Importer\KeyVisitor;
use PHPSemVer\DataTree\Importer\ParentVisitor;

/**
 * Basic functionality for wrapper.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
abstract class AbstractWrapper
{
    protected $_base;
    protected $_cacheFactory;
    protected $_parserExceptions;
    protected $excludePattern;

    public function __construct($base)
    {
        if ( ! $base) {
            throw new \InvalidArgumentException(
                'Please provide a base. Can not be empty.'
            );
        }
        $this->_base = $base;
    }

    abstract public function getAllFileNames();

    public function getExcludePattern()
    {
        return (array)$this->excludePattern;
    }

    public function setExcludePattern($pattern)
    {
        $this->excludePattern = $pattern;
    }

    abstract public function getBasePath();

    /**
     * Get version.
     *
     * @return mixed
     */
    public function getBase()
    {
        return $this->_base;
    }

    public function getDataTree()
    {
        ini_set('xdebug.max_nesting_level', 3000);

        $parser = new Parser(new Emulative);

        $dataTree = [];

        $nameResolver = new NodeTraverser();
        $nameResolver->addVisitor(new NameResolver);
        $nameResolver->addVisitor(new ParentVisitor());
        $nameResolver->addVisitor(new KeyVisitor());
        foreach ($this->getAllFileNames() as $sourceFile) {
            if ( ! preg_match('/\.php$/i', $sourceFile)) {
                continue;
            }

            $sourceFile = realpath($sourceFile);

            try {
                $tree = $parser->parse(file_get_contents($sourceFile));
                $tree = $nameResolver->traverse($tree);

                $dataTree = array_merge($dataTree, $tree);
            } catch (Error $e) {
                $e->setRawMessage($e->getRawMessage() . ' in file ' . $sourceFile);
                throw $e;
            }
        }

        return $dataTree;
    }
}