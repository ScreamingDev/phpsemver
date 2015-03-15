<?php

namespace PHPSemVer\Assertions;

use PDepend\Source\Language\PHP\PHPBuilder;

interface AssertionInterface
{
    public function __construct( PHPBuilder $previousBuilder, PHPBuilder $latestBuilder );

    public function process();
}