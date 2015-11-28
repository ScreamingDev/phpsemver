<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 27.11.15
 * Time: 18:35
 */

namespace PHPSemVer\Parser\PHP;


interface ParserInterface
{
    public function getTarget();

    public function getAST();
}