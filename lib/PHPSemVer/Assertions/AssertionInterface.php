<?php

namespace PHPSemVer\Assertions;

use PDepend\Source\Language\PHP\PHPBuilder;
use PHPSemVer\DataTree\DataNode;

interface AssertionInterface
{
    public function __construct( DataNode $previous, DataNode $latest );

    public function process();
}