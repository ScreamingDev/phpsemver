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
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.2.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */

namespace PHPSemVer\Wrapper;

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPSemVer\DataTree\Importer\KeyVisitor;
use PHPSemVer\DataTree\Importer\ParentVisitor;

/**
 * Basic functionality for wrapper.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.2.0/LICENSE.md MIT License
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

    public function addExcludePattern($getRegExp)
    {
        $this->excludePattern[] = $getRegExp;
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

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP5);

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

                $dataTree = $this->mergeTrees($dataTree, $tree);
            } catch (Error $e) {
                $e->setRawMessage($e->getRawMessage() . ' in file ' . $sourceFile);
                throw $e;
            }
        }

        return $dataTree;
    }

    /**
     * Merge two Node trees.
     *
     * @param $tree
     * @param $dataTree
     *
     * @return mixed
     */
    protected function mergeTrees($dataTree, $tree)
    {
        foreach ($tree as $key => $node) {
            if ( ! isset( $dataTree[$key] )) {
                $dataTree[$key] = $node;
            }

            foreach ($node->getSubNodeNames() as $subNode) {
                if ( ! isset( $dataTree[$key]->$subNode )) {
                    $dataTree[$key]->$subNode = $node->$subNode;
                }

                if ( ! is_array($dataTree[$key]->$subNode)
                     && ! is_array($node->$subNode)
                ) {
                    continue;
                }

                $dataTree[$key]->$subNode = array_merge(
                    (array) $dataTree[$key]->$subNode,
                    (array) $node->$subNode
                );
            }
        }

        return $dataTree;
    }
}