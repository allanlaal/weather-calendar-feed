<?php

// Does a few tricks with a compass.
//
// Conventions:
// var compass = e.g. NE (North East)
// var degrees = e.g. 45 (scale 0-360)
//
// Evert Mouw <post@evert.net>
// 2019-01

namespace Compass
{

// this named array of degrees is used by multiple functions
$directions['N'] = 0.0;
$directions['NNE'] = 22.5;
$directions['NE'] = 45.0;
$directions['ENE'] = 67.5;
$directions['E'] = 90.0;
$directions['ESE'] = 112.5;
$directions['SE'] = 135.0;
$directions['SSE'] = 157.5;
$directions['S'] = 180.0;
$directions['SSW'] = 202.5;
$directions['SW'] = 225.0;
$directions['WSW'] = 247.5;
$directions['W'] = 270.0;
$directions['WNW'] = 292.5;
$directions['NW'] = 315.0;
$directions['NNW'] = 337.5;

/**
 * @param string $winddirection
 * @return float
 * @desc returns the compass degrees of the wind direction
 */
function compass2degrees($compass)
{
	global $directions;
	return $directions[$compass];
}

function compass2degrees_inplace(&$compass)
{	global $directions;
	$compass = $directions[$compass];
}

/**
 * @param float $degrees
 * @return string
 * @desc returns e.g. NW when degrees is 45
 */
function degrees2compass($degrees)
{
	global $directions;
	foreach ( $directions as $key => $slice )
	{
		if ( $degrees >= $slice && $degrees < ( $slice + 22.5 ) )
		{
			return $key;
		}
	}
	return '?';
}

/**
 * @param float $degrees
 * @return float 
 * @desc returns 180 degrees opposite direction
 */
function opposite($degrees)
{
	$opposite = $degrees + 180;
	if ( $opposite >= 360 ) $opposite -= 360;
	return $opposite;
}

/**
 * @param float $degrees
 * @return string 
 * @desc returns an arrow from the degrees
 */
function arrow($degrees)
{
	global $directions;
	if ($degrees > $directions['NNW'] && $degrees <= $directions['NNE']) return "⇧";
	if ($degrees > $directions['NNE'] && $degrees <= $directions['ENE']) return "⇗";
	if ($degrees > $directions['ENE'] && $degrees <= $directions['ESE']) return "⇨";
	if ($degrees > $directions['ESE'] && $degrees <= $directions['SSE']) return "⇘";
	if ($degrees > $directions['SSE'] && $degrees <= $directions['SSW']) return "⇩";
	if ($degrees > $directions['SSW'] && $degrees <= $directions['WSW']) return "⇙";
	if ($degrees > $directions['WSW'] && $degrees <= $directions['WNW']) return "⇦";
	if ($degrees > $directions['WNW'] && $degrees <= $directions['NNW']) return "⇖";
	return "↹";
}

/**
 * @desc testing
 */
function test()
{
	$out = '';
	$out .= degrees2compass(45) . "\n";
	$out .= compass2degrees('NE') . "\n";
	$out .= opposite(45) . "\n";
	$out .= arrow(45) . "\n";
	echo $out;
}

//end namespace Compass
}
?>
