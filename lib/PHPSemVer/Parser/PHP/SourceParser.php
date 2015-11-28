<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 27.11.15
 * Time: 18:32
 */

namespace PHPSemVer\Parser\PHP;


use PHPSemVer\PHPAST;

class SourceParser extends AbstractParser implements ParserInterface
{

    protected function parse()
    {
        $token = token_get_all($this->getTarget());

        $parser = new TokenParser($token);

        $this->ast = $parser->getAST();
    }
}