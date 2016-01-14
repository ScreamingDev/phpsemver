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
 * @copyright 2015-2016 Mike Pretzlaw. All rights reserved.
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.2.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */

namespace PHPSemVer\Wrapper;

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPSemVer\Config\Filter;
use PHPSemVer\DataTree\Importer\KeyVisitor;
use PHPSemVer\DataTree\Importer\ParentVisitor;

/**
 * Basic functionality for wrapper.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015-2016 Mike Pretzlaw. All rights reserved.
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.2.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
abstract class AbstractWrapper
{
    protected $_base;
    protected $fileNames;
    protected $filter;

    public function __construct($base)
    {
        if ( ! $base) {
            throw new \InvalidArgumentException(
                'Please provide a base. Can not be empty.'
            );
        }

        $this->_base = $base;
    }

    abstract protected function fetchFileNames();

    public function getAllFileNames() {
        if (null === $this->fileNames) {
            $this->fetchFileNames();
        }

        $fileNames = [];
        foreach ($this->fileNames as $shortName => $realName) {
            if ( $this->getFilter() && ! $this->getFilter()->matches($shortName)) {
                continue;
            }

            $fileNames[$shortName] = $realName;
        }

        return $fileNames;
    }

    /**
     * Get the configured filter.
     *
     * @return Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    public function setFilter($pattern)
    {
        $this->filter = $pattern;
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

        $nameResolver = new NodeTraverser();
        $nameResolver->addVisitor(new NameResolver);
        $nameResolver->addVisitor(new ParentVisitor());
        $nameResolver->addVisitor(new KeyVisitor());
        foreach ($this->getAllFileNames() as $relativePath => $sourceFile) {
            if ( $this->getFilter() && ! $this->getFilter()->matches($relativePath)) {
                continue;
            }

            if ( ! preg_match('/\.php$/i', $sourceFile)) {
                continue;
            }

            try {
                $tree = $parser->parse(file_get_contents($sourceFile));

                yield $nameResolver->traverse($tree);
            } catch (Error $e) {
                $e->setRawMessage($e->getRawMessage() . ' in file ' . $sourceFile);
                throw $e;
            }
        }
    }

    /**
     * Merge two Node trees.
     *
     * @param $tree
     * @param $dataTree
     *
     * @return mixed
     */
    public function mergeTrees($dataTree, $tree)
    {
        foreach ($tree as $key => $node) {
            if (is_numeric($key)) {
                $key = uniqid();
            }

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