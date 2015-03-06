<?php

namespace PHPSemVer\Rules;


use PDepend\Source\Builder\Builder;

class Semver2Rule {
	public function process( $previousBuilder, $latestBuilder ) {
		if ( ! ( $previousBuilder instanceof Builder ) ) {
            throw new \InvalidArgumentException(
	            'Please support an instance of Builder'
            );
		}

		if ( ! ( $latestBuilder instanceof Builder ) ) {
			throw new \InvalidArgumentException(
				'Please support an instance of Builder'
			);
		}

		return true;
	}
}