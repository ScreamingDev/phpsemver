<?php

namespace PHPSemVer\Helper;


class Ast {
	public static function artifactListToAssocArray( $list ) {
		$assocArray = array();
		foreach ( $list as $ast ) {
			$assocArray[ $ast->getName() ] = $ast;
		}

		return $assocArray;
	}
}