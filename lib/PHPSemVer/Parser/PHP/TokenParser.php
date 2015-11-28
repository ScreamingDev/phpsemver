<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 27.11.15
 * Time: 18:39
 */

namespace PHPSemVer\Parser\PHP;


use PHPSemVer\AST\PhpAst;
use PHPSemVer\Parser\PHP\TokenParser\MainContext;
use PHPSemVer\Parser\PHP\TokenParser\NamespaceParser;

class TokenParser extends AbstractParser implements ParserInterface
{
    public function getMapping()
    {

    }

    public function parseNamespace($token)
    {
        // check for name and opening brace
        $name      = '';
        $curlyOpen = false;
        while (current($token) != ';') {
            $target = current($token);

            if ( ! is_array($target)) {
                continue;
            }

            if (T_STRING == $target[0] || T_NS_SEPARATOR == $target[0]) {
                $name .= $target[1];
            }

            if ($target[0] == T_CURLY_OPEN) {
                $curlyOpen = true;
                break;
            }

            next($token);
        }

        $namespace = array_slice($token, key($token) + 1);

        if ($curlyOpen) {
            $level = 1;

            foreach ($namespace as $pos => $token) {
                if ($token == '}') {
                    $level--;
                }

                if (is_array($token) && T_CURLY_OPEN == $token[0]) {
                    $level++;
                }

                if ($level <= 0) {
                    $namespace = array_slice($namespace, 0, $pos + 1);
                    break;
                }
            }
        }

        $parser = new NamespaceParser($namespace);
        $this->getAST()->getNamespace($name)->append($parser->getAST());
    }

    protected function parse()
    {
        $context = new MainContext(null);
        $context->delegate($this->getTarget());

        $this->ast = $context->getAST();
    }
}