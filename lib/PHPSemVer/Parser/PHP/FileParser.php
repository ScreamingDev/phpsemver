<?php

namespace PHPSemVer\Parser\PHP;


class FileParser extends SourceParser implements ParserInterface
{


    protected function parse()
    {
        $content = file_get_contents($this->getTarget());

        $parser = new SourceParser($content);

        $this->ast = $parser->getAST();
    }
}