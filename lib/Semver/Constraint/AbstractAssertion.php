<?php

namespace Semver\Constraint;


abstract class AbstractAssertion {
	abstract function run();

	protected function __( $text ) {
		$text = dgettext( SEMVER_TEXTDOMAIN, $text );

		if ( func_num_args() > 1 ) {
			$text = vsprintf( $text, array_slice( func_get_args(), 1 ) );
		}

		return $text;
	}
}