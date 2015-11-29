<?php

namespace PHPSemVer\DataTree\Importer;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PHPSemVer\DataTree\DataNode;

class NikicParser
{
    /**
     * @param Stmt\Class_ $node
     * @param DataNode    $dataTree
     */
    public function importStmtClass($node, $dataTree)
    {
        $dataTree->classes[$node->name] = $node;
    }

    /**
     * @param Stmt\Function_ $node
     * @param DataNode       $dataTree
     */
    public function importStmtFunction($node, $dataTree)
    {
        $dataTree->functions[$node->name] = $node;
    }

    /**
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
            /** @var Stmt $node */
            $type = str_replace('_', '', $node->getType());

            $methodName = 'import'.$type;
            if ( ! method_exists($this, $methodName)) {
                continue;
            }

            $this->$methodName($node, $dataTree);
        }
    }

    /**
     * @param Stmt\Use_ $node
     * @param DataNode  $dataTree
     */
    public function importStmtUse($node, $dataTree)
    {
        /** @var Stmt\UseUse $useUse */
        $useUse = current($node->uses);

        $dataTree->usages[$useUse->name->toString()] = $node;
    }
}