<?php

namespace PHPSemVer\Helper;


use PDepend\Source\AST\ASTArtifactList;

class Ast {
    /**
     * @param ASTArtifactList $list
     *
     * @return array
     */
	public static function artifactListToAssocArray( $list ) {
		$assocArray = array();
		foreach ( $list as $ast ) {
			$assocArray[ $ast->getName() ] = $ast;
		}

		return $assocArray;
	}
}