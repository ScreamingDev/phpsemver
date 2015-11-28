<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 28.11.15
 * Time: 11:29
 */

namespace PHPSemVer\Parser\PHP\TokenParser;


use PHPSemVer\AST\NamespaceNode;
use PHPSemVer\AST\PhpAst;

class NamespaceContext extends AbstractContext
{
    protected $depth         = 0;
    protected $hasCurlyBrace = false;
    protected $name          = '';

    public function delegate($target = [])
    {
        foreach ($target as $token) {
            if (';' == $token) {
                break;
            }

            if ( ! is_array($token)) {
                continue;
            }

            if (T_CURLY_OPEN == $token[0]) {
                $this->hasCurlyBrace = true;
                break;
            }

            if (T_NS_SEPARATOR == $token[0] || T_STRING == $token[0]) {
                $this->name .= $token[1];
            }
        }

        /** @var PhpAst $root */
        $root = $this->getAST()->getRootNode();
        $this->ast = $root->getNamespace($this->name);

        return parent::delegate($target);
    }

    protected function parseToken($singleToken)
    {
        if ( ! $this->hasCurlyBrace) {
            return;
        }

        if ('}' == $singleToken) {
            $this->depth--;
        }

        if ($this->depth < 0) {
            $this->terminated = true;

            return;
        }

        if ( ! is_array($singleToken)) {
            return;
        }

        if (T_CURLY_OPEN == $singleToken[0]) {
            $this->depth++;
        }

    }
}