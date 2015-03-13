<?php

namespace PHPSemVer\Rules;

use PDepend\Source\Language\PHP\PHPBuilder;

interface RuleInterface
{
    public function __construct( PHPBuilder $previousBuilder, PHPBuilder $latestBuilder );

    public function process();
}