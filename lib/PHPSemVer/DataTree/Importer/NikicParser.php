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

namespace PHPSemVer\DataTree\Importer;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PHPSemVer\DataTree\DataNode;

/**
 * Parse AST of the Nikic Parser.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015 Mike Pretzlaw
 * @license   https://github.com/sourcerer-mike/phpsemver/tree/3.0.0/LICENSE.md MIT License
 * @link      https://github.com/sourcerer-mike/phpsemver/
 */
class NikicParser
{
    /**
     * Import class statement.
     *
     * @param Stmt\Class_ $node
     * @param DataNode    $dataTree
     */
    public function importStmtClass($node, $dataTree)
    {
        $dataTree->classes[$node->name] = $node;
    }

    /**
     * Import function statement.
     *
     * @param Stmt\Function_ $node
     * @param DataNode       $dataTree
     */
    public function importStmtFunction($node, $dataTree)
    {
        $dataTree->functions[$node->name] = $node;
    }

    /**
     * Import namespace statement.
     *
     * @param Stmt\Namespace_ $node
     * @param DataNode        $dataTree
     */
    public function importStmtNamespace($node, $dataTree)
    {
        $name = $node->name;

        if ($name instanceof Name) {
            $name = $name->toString();
        }

        if ( ! isset( $dataTree->namespaces[$name] )) {
            $dataTree->namespaces[$name] = new DataNode();
        }

        $this->importStmts($node->stmts, $dataTree->namespaces[$name]);
    }

    public function importStmts($tree, $dataTree)
    {
        foreach ($tree as $node) {
            /* @var Stmt $node */
            $type = str_replace('_', '', $node->getType());

            $methodName = 'import'.$type;
            if ( ! method_exists($this, $methodName)) {
                continue;
            }

            $this->$methodName($node, $dataTree);
        }
    }

    /**
     * Import use statement.
     *
     * @param Stmt\Use_ $node
     * @param DataNode  $dataTree
     */
    public function importStmtUse($node, $dataTree)
    {
        /* @var Stmt\UseUse $useUse */
        $useUse = current($node->uses);

        $dataTree->usages[$useUse->name->toString()] = $node;
    }
}