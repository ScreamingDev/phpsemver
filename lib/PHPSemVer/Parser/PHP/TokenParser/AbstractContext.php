<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 28.11.15
 * Time: 11:35
 */

namespace PHPSemVer\Parser\PHP\TokenParser;


use PHPSemVer\AST\PhpAst;

abstract class AbstractContext
{
    protected $ast        = null;
    protected $parent;
    protected $terminated = false;

    public function __construct($parent = null)
    {
        if ( ! $parent) {
            $this->ast = new PhpAst();
        }

        $this->parent = $parent;
    }

    public function delegate($target = [])
    {
        $count = count($target);
        for ($pos = 0; $pos < $count; $pos++) {
            $token = $target[$pos];

            if ( ! is_array($token)) {
                continue;
            }

            $name = substr(token_name($token[0]), 2);

            $name = ucwords(strtolower($name), '_');
            $name = str_replace('_', '', $name);

            if ( ! $name) {
                continue;
            }

            $className = __CLASS__.'\\'.$name.'Context';

            if ( ! class_exists($className)) {
                continue;
            }

            /** @var AbstractContext $context */
            $context = new $className($this);

            $pos += $context->delegate(array_slice($this->getTarget(), $pos));

            $this->parseToken($token[$pos]);

            if ($this->isTerminated()) {
                break;
            }
        }

        return $pos;
    }

    abstract protected function parseToken($singleToken);

    public function isTerminated()
    {
        return (bool) $this->terminated;
    }

    public function getAST()
    {
        if ($this->ast || null == $this->getParent()) {
            return $this->ast;
        }

        return $this->getParent()->getAST();
    }

    /**
     * @return AbstractContext
     */
    public function getParent()
    {
        return $this->parent;
    }


}