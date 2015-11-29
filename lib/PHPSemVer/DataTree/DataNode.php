<?php

namespace PHPSemVer\DataTree;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;

class DataNode
{
    /**
     * @var Class_[]
     */
    public $classes    = [];

    /**
     * @var Function_[]
     */
    public $functions  = [];

    /**
     * @var DataNode[]
     */
    public $namespaces = [];

    /**
     * @var Use_[]
     */
    public $usages     = [];
}