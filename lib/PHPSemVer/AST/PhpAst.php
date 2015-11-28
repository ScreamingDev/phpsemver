<?php

namespace PHPSemVer\AST;

class PhpAst extends AbstractAst
{
    protected $namespaces = [];

    public function getNamespace($name)
    {
        $path = explode('\\', $name);

        $next = array_shift($path);

        if ( ! isset( $this->namespaces[$next] )) {
            $this->namespaces[$next] = new NamespaceNode($this);
            $this->namespaces[$next]->setName($next);
        }

        if ( ! $path) {
            return $this->namespaces[$next];
        }

        return $this->namespaces[$next]->getNamespace($path);
    }
}