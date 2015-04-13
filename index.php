<?php
/**
 * @desc Display yr.no weather forecasts with highly customizable Event titles in your Google Calendar, phone or other iCalendar app
 * @since 2015-04-13
 * @author Allan Laal <allan@permanent.ee>
 * @example http://weather.is.not.permanent.ee/?location=Estonia/Harjumaa/Tallinn&filename=weather.ics
 * @link https://github.com/allanlaal/weather-calendar-feed
 */
$version = '20150410T000000Z'; // modify this when you make changes in the code!
$location = param('location', 'Estonia/Harjumaa/Tallinn');

require_once('./vendor/yr-php-library/autoload.php');
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//echo '<pre>';

// buffer output so if anything fails, it wont display a partial calendar
$out = "BEGIN:VCALENDAR\r\n";
$out .= "PRODID:-//Permanent Solutions Ltd//Weather Calendar//EN\r\n";
$out .= "VERSION:5.1.4\r\n";
$out .= "CALSCALE:GREGORIAN\r\n";
$out .= "METHOD:PUBLISH\r\n";
$out .= "URL:https://github.com/allanlaal/weather-calendar-feed\r\n";
$out .= "X-WR-CALNAME:Weather\r\n";
$out .= "X-WR-CALDESC:Display yr.no weather forecasts in Google Calendar.\r\n";
$out .= "X-LOTUS-CHARSET:UTF-8\r\n";


// cache forecasts:
$yr = Yr\Yr::create($location, "/tmp");
$days = array();
foreach($yr->getPeriodicForecasts(strtotime("now -3 days"), strtotime("now +100 days") - 1/*sec*/) as $forecast)
{
	$days[$forecast->getFrom()->format('Y-m-d')][$forecast->getFrom()->format('H:i')] = $forecast;
}
	
foreach ($days as $date => $day)
{
	$out .= "BEGIN:VEVENT\r\n";
	$out .= "DTSTART;VALUE=DATE:".date('Ymd', strtotime($date))."\r\n";
	$out .= "DTEND;VALUE=DATE:".date('Ymd', strtotime($date.' +1 days'))."\r\n";
	$out .= "DTSTAMP:".date('Ymd\THis\Z')."\r\n";
	$out .= "UID:Permanent-Weather-".date('Ymd', strtotime($date))."-$version\r\n";
	$out .= "CLASS:PUBLIC\r\n";
	$out .= "CREATED:$version\r\n";
	$out .= "LOCATION:".str_replace('/', ', ', $location)."\r\n"; //@https://www.ietf.org/rfc/rfc2445.txt
	$out .= "LAST-MODIFIED:$version\r\n";
	$out .= "SEQUENCE:0\r\n";
	$out .= "STATUS:CONFIRMED\r\n";
	
	$out .= 'DESCRIPTION:';
	$temp_max = -1337;
	$temp_min = 1337;
	$precipitation = 0;
	$wind_max = -1337;
	foreach ($day as $hour => $forecast)
	{
		$out .= $hour.'\n';
		$out .= 'Temperature: '.$forecast->getTemperature().' °C\n';
		$out .= 'Precipitation: '.$forecast->getPrecipitation().' mm\n';
		$out .= 'Conditions: '.$forecast->getSymbol().'\n';
		$out .= 'Wind: '.$forecast->getWindSpeed().' m/s from '.$forecast->getWindDirection().'\n';
		$out .= 'Pressure: '.$forecast->getPressure().' hPa\n';
		$out .= '\n';
		
		if ($temp_max < $forecast->getTemperature())
		{
			$temp_max = $forecast->getTemperature();
		}
		
		if ($temp_min > $forecast->getTemperature())
		{
			$temp_min = $forecast->getTemperature();
		}
		
		if ($wind_max < $forecast->getWindSpeed())
		{
			$wind_max = $forecast->getWindSpeed();
		}
		
		$precipitation += $forecast->getPrecipitation();
	}
	$out .= "\r\n";

	// my phone can fit 17 chars, my GoogleCalendar 19 chars:
	//	12345678901234567
	//	-22|-22 12☔ 11.2⇶ <- worst case scenario lengthwise
	$out .= "SUMMARY:$temp_max|$temp_min {$precipitation}☔ {$wind_max}⇶\r\n";	

	
	$out .= "TRANSP:OPAQUE\r\n";
	$out .= "END:VEVENT\r\n";
			
}

$out .= 'END:VCALENDAR';

//$out = str_replace('\n', "\n", $out);

header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename='.param('filename'));
echo $out;



/**
 * @param string $name
 * @param string $default
 * @return string
 * @desc GET an URL parameter
 */
function param($name, $default='')
{
//	echo "&$name=$default"; // builds URL parameters with the default values
	
	if (
		isset($_GET[$name])
		&& 
		!empty($_GET[$name])
	)
	{
		$out = filter_input(INPUT_GET, $name, FILTER_SANITIZE_STRING);
	}
	else
	{
		$out = $default;
	}
	
	return $out;
}