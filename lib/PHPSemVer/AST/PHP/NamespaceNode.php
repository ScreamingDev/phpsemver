<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 28.11.15
 * Time: 14:29
 */

namespace PHPSemVer\AST;


class NamespaceNode extends AbstractAst
{
    protected $name;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getRoot()
    {

    }
}