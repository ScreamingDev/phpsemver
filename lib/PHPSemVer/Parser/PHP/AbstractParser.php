<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 27.11.15
 * Time: 18:28
 */

namespace PHPSemVer\Parser\PHP;


use PHPSemVer\AST\PhpAst;

abstract class AbstractParser {

	protected $ast = false;
	protected $target;

	public function __construct( $target ) {
		$this->target = $target;
	}

	/**
	 * @return PhpAst
	 */
	public function getAST() {
		if ( false === $this->ast ) {
			$this->parse();
		}

		return $this->ast;
	}

	protected abstract function parse();

	/**
	 * @return mixed
	 */
	public function getTarget() {
		return $this->target;
	}
}