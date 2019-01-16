<?php

# convert windspeed in m/s to beaufort scale
# https://stackoverflow.com/questions/24812851/using-comparison-operators-in-php-switch
# https://en.wikipedia.org/wiki/Beaufort_scale
# Evert Mouw <post@evert.net>
# 2019-01

namespace Windspeed
{

function beaufort($windspeed)
{

switch (true) {
	case $windspeed < 0.5 :
		$beaufort = 0 ;
		break;
	case $windspeed < 1.6 :
		$beaufort = 1 ;
		break;
	case $windspeed < 3.4 :
		$beaufort = 2 ;
		break;
	case $windspeed < 5.5 :
		$beaufort = 3 ;
		break;
	case $windspeed < 8.0 :
		$beaufort = 4 ;
		break;
	case $windspeed < 10.8 :
		$beaufort = 5 ;
		break;
	case $windspeed < 13.9 :
		$beaufort = 6 ;
		break;
	case $windspeed < 17.2 :
		$beaufort = 7 ;
		break;
	case $windspeed < 20.8 :
		$beaufort = 8 ;
		break;
	case $windspeed < 24.5 :
		$beaufort = 9 ;
		break;
	case $windspeed < 28.5 :
		$beaufort = 10 ;
		break;
	case $windspeed < 32.7 :
		$beaufort = 11 ;
		break;
	case $windspeed > 32.6 :
		$beaufort = 12 ;
		break;
	default:
		$beaufort = "?";
		break;
}
return $beaufort;

//end function
}

//end namespace
}

?>
