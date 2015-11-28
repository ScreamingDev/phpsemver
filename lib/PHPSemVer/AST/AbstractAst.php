<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 28.11.15
 * Time: 15:38
 */

namespace PHPSemVer\AST;


abstract class AbstractAst
{
    protected $parent;

    public function __construct($parent = null)
    {
        $this->parent = $parent;
    }

    public function getRootNode()
    {
        if ($this->getParentNode()) {
            return $this->getParentNode();
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentNode()
    {
        return $this->parent;
    }
}