<?php

namespace PHPSemVer\Constraint;


abstract class AbstractAssertion {
	abstract function run();

    protected function translate( $text )
    {
		$text = dgettext( PHPSEMVER_TEXTDOMAIN, $text );

		if ( func_num_args() > 1 ) {
			$text = vsprintf( $text, array_slice( func_get_args(), 1 ) );
		}

		return $text;
	}
}