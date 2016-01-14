<?php
/**
 * Class for parent visitor.
 *
 * LICENSE: This source file is subject to the MIT license
 * that is available through the world-wide-web at the following URI:
 * https://opensource.org/licenses/MIT. If you did not receive a copy
 * of the PHP License and are unable to obtain it through the web, please send
 * a note to pretzlaw@gmail.com so we can mail you a copy immediately.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015-2016 Mike Pretzlaw. All rights reserved.
 * @license   http://github.com/sourcerer-mike/phpsemver/LICENSE.md MIT License
 * @link      http://github.com/sourcerer-mike/phpsemver
 */

namespace PHPSemVer\DataTree\Importer;


use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

/**
 * Connect statements with their parent.
 *
 * @author    Mike Pretzlaw <pretzlaw@gmail.com>
 * @copyright 2015-2016 Mike Pretzlaw. All rights reserved.
 * @license   http://github.com/sourcerer-mike/phpsemver/LICENSE.md MIT License
 * @link      http://github.com/sourcerer-mike/phpsemver
 */
class ParentVisitor extends NodeVisitorAbstract {
    private $stack;
    public function beginTraverse(array $nodes) {
        $this->stack = [];
    }
    public function enterNode(Node $node) {
        if (!empty($this->stack)) {
            $node->setAttribute('parent', $this->stack[count($this->stack)-1]);
        }
        $this->stack[] = $node;
    }
    public function leaveNode(Node $node) {
        array_pop($this->stack);
    }
}