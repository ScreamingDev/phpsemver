<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 28.11.15
 * Time: 12:21
 */

namespace PHPSemVer\Parser\PHP\TokenParser;


class PhpContext extends AbstractContext
{
    protected function parseToken($singleToken)
    {
        if (is_array($singleToken) && T_CLOSE_TAG == $singleToken) {
            $this->terminated = true;
        }
    }
}