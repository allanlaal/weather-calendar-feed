<?php

// circular (polar) calculations on compass degrees
// based on previous C code from ???
// Evert Mouw <post@evert.net>
// 2019-01

namespace Circular {

	function fixdegrees($degrees){
		if ( $degrees < 0 ) $degrees += 360;
		if ( $degrees >= 360 ) $degrees -= 360;
		return $degrees;
	}

	function distancedegrees($degrees_A, $degrees_B){
		$distance = $degrees_A - $degrees_B;
		// e.g. 2-350 = -348, but real distance is 12
		if ($distance < 0){
			$distance *= -1;
		}
		// now we've got 348
		if ($distance > 180){
			$distance = 360 - $distance;
		}
		// back to 12
		return $distance;
	}

	function degrees2radians($degrees){
		return $degrees / (180.0 / pi());
	}

	function radians2degrees($radians){
		return $radians * (180.0 / pi());
	}

	function degrees_circular_avg($array){
		// get average degree with circular calculations
		// skips negative values in the array (!!)
		// e.g. what is +20 degrees and 340 degrees on average?
		// https://stackoverflow.com/questions/491738/how-do-you-calculate-the-average-of-a-set-of-circular-data
		// https://en.wikipedia.org/wiki/Mean_of_circular_quantities
		// first get the sums and n
		$n = 0;
		$sum_sin = 0;
		$sum_cos = 0;
		foreach ($array as $arr ){
			if ( $arr >= 0 ){
				$n += 1;
				$sum_sin += sin(degrees2radians($arr));
				$sum_cos += cos(degrees2radians($arr));
			}
		}
		// yeah getting the mean compass
		$radians = atan2( $sum_sin / $n, $sum_cos / $n);
		$degrees = radians2degrees($radians);
		return fixdegrees($degrees);
	}
}

?>
