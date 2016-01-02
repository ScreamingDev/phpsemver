<?php
/**
 * Class for key visitor.
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * https://opensource.org/licenses/MIT. If you did not receive a copy
 * of the PHP License and are unable to obtain it through the web, please send
 * a note to pretzlaw@gmail.com so we can mail you a copy immediately.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2016 Mike Pretzlaw
 * @license   http://github.com/sourcerer-mike/phpsemver/LICENSE.md MIT License
 * @link      http://github.com/sourcerer-mike/phpsemver
 */

namespace PHPSemVer\DataTree\Importer;


use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\NodeAbstract;
use PhpParser\NodeVisitorAbstract;

/**
 * Turn statement array in associative array.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2016 Mike Pretzlaw
 * @license   http://github.com/sourcerer-mike/phpsemver/LICENSE.md MIT License
 * @link      http://github.com/sourcerer-mike/phpsemver
 */
class KeyVisitor extends NodeVisitorAbstract
{
    public function Stmt_ClassHash(Stmt\Class_ $node)
    {
        $node->stmts = $this->beforeTraverse($node->stmts);

        return 'class_'.$node->name;
    }

    /**
     * Turn numbers into unique string.
     *
     * @param NodeAbstract[] $nodes
     *
     * @return NodeAbstract[]
     */
    public function beforeTraverse(array $nodes)
    {
        $keyedNodes = [];

        foreach ($nodes as $node) {
            $hashFunc = $node->getType().'Hash';

            if ( ! method_exists($this, $hashFunc)) {
                $keyedNodes[] = $node;

                continue;
            }

            $keyedNodes[$this->$hashFunc($node)] = $node;
        }

        return $keyedNodes;
    }

    public function Stmt_ClassMethodHash(Stmt\ClassMethod $node)
    {
        return 'method_'.$node->name;
    }

    public function Stmt_FunctionHash(Stmt\Function_ $node)
    {
        return 'func_'.$node->name;
    }

    public function Stmt_InterfaceHash(Stmt\Interface_ $node)
    {
        return 'interface_'.$node->name;
    }

    public function Stmt_NamespaceHash(Stmt\Namespace_ $node)
    {
        $node->stmts = $this->beforeTraverse($node->stmts);

        return 'namespace_'.$node->name;
    }

    public function Stmt_TraitHash(Stmt\Trait_ $node)
    {
        return 'trait_'.$node->name;
    }

}