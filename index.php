<?php
/**
 * @desc Display yr.no weather forecasts with highly customizable Event titles in your Google Calendar, phone or other iCalendar app
 * @since 2015-04-13
 * @author Allan Laal <allan@permanent.ee>
 * @modified Evert Mouw <post@evert.net> (2019-01-09)
 * @example http://weather.is.not.permanent.ee/?location=Estonia/Harjumaa/Tallinn&usemanyicons=no&filename=weather.ics
 * @link https://github.com/allanlaal/weather-calendar-feed
 */
$version = '20190108T000000Z'; // modify this when you make changes in the code!
$location = param('location', 'Netherlands/Gelderland/Elspeet');
// use multiple weather emoji in the summary, or just one weather emoji for the average over the day
// note: first i used a boolean, but for some reason it didn't work... revertint to yes|no string!
$usemanyicons = param('usemanyicons', 'yes');

require_once('./vendor/yr-php-library/autoload.php');
require_once('./vendor/php-moon-phase/MoonPhase.php');
require_once ("circular.php");
require_once ("compass.php");
require_once ("beaufort.php");

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
$out .= "X-WR-CALDESC:Display yr.no weather forecasts (usemanyicons=".$usemanyicons.").\r\n";
$out .= "X-LOTUS-CHARSET:UTF-8\r\n";

// cache forecasts:
$yr = Yr\Yr::create($location, "./tmp");
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
	$out .= "TRANSP:TRANSPARENT\r\n";
	$out .= "X-MICROSOFT-CDO-BUSYSTATUS:FREE\r\n";
	$out .= "X-MICROSOFT-CDO-INTENDEDSTATUS:FREE\r\n";
	$out .= "X-MICROSOFT-CDO-ALLDAYEVENT:TRUE\r\n";
	
	// get the moon phase
	$MyMoon = new Solaris\MoonPhase(strtotime($date));
	$moon_icon = $MyMoon->phase_emoji();
	$moon_desc = 'Moon phase: ' . $MyMoon->phase_name() . '\n';
	if ( strtotime($date) > $MyMoon->full_moon() ) {
		$next_full_moon = $MyMoon->next_full_moon();
	} else {
		$next_full_moon = $MyMoon->full_moon();
	}
	$moon_desc .= 'Next full moon: ' . gmdate('Y-m-d', $next_full_moon) . '\n';
	unset ($MyMoon);

	$out .= 'DESCRIPTION:';
	$temp_max = -1337;
	$temp_min = 1337;
	$precipitation = 0;
	$wind_max = -1337;
	$manyicons = "";
	$conditions_wholeday = '';
	$temperature_wholeday = '';
	$windspeed_wholeday = '';
	$winddirection_wholeday = '';
	foreach ($day as $hour => $forecast)
	{
		$windspeed = $forecast->getWindSpeed();
		$windspeed_wholeday .= $windspeed.'#';
		$beaufort = Windspeed\beaufort($windspeed);
		$conditions = $forecast->getSymbol();
		$conditions_wholeday .= $conditions.'#';
		$temperature = $forecast->getTemperature();
		$temperature_wholeday .= $temperature.'#';
		$winddirection = $forecast->getWindDirection();
		$winddirection_wholeday .= $winddirection.'#';
		
		$daypart_icon = weather_icon($temperature,score_weather_conditions($conditions));
		$manyicons .= $daypart_icon;
		
		$out .= $hour.' '.$daypart_icon.'\n';
		$out .= 'Temperature: '.$temperature.' °C\n';
		$out .= 'Precipitation: '.$forecast->getPrecipitation().' mm\n';
		$out .= 'Conditions: '.$conditions.'\n';
		$out .= 'Wind: '.$beaufort.' B from '.$winddirection.'\n';
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
		
		#if ($wind_max < $forecast->getWindSpeed())
		#{
		#	$wind_max = $forecast->getWindSpeed();
		#}
		
		$precipitation += $forecast->getPrecipitation();
	}
	$out .= $moon_desc;
	$out .= "\r\n";

	// my phone can fit 20 chars, my GoogleCalendar 19 chars:
	//	1234567890123456789
	//	-22|-22 12☔ 11.2⇶ <- worst case scenario lengthwise		
	
	// use multiple weather emoji or create one weather emoji from the average
	$weather_icon = '⚠️'; // default emoji, this one you should not see...
	if ($usemanyicons == 'yes')
	{
		$weather_icon = $manyicons;
	} else {
		$cond_total = 0;
		$cond_count = 0;
		$temp_total = 0;
		
		foreach ($conditions_all as $cond)
		{
			$cond_total += score_weather_conditions($cond);
			$cond_count += 1;
		}
		$temperature_all = explode('#',substr($temperature_wholeday,0,-1));
		foreach ($temperature_all as $temp)
		{
			$temp_total += score_weather_conditions($cond);
		}
		$cond_avg = round ($cond_total / $cond_count);
		$temp_avg = round ($temp_total / $cond_count);
		$weather_icon = weather_icon($temp_avg,$cond_avg);
	}
	
	// determine the average wind speed and direction
	$windspeed_all = explode('#',substr($windspeed_wholeday,0,-1));
	$totalspeed = 0;
	$countwind = 0;
	foreach ($windspeed_all as $speed)
	{
		$totalspeed += $speed;
		$countwind += 1;
	}
	$windspeed = round ($totalspeed / $countwind);
	$beaufort = Windspeed\beaufort($windspeed);
	#$out .= "DEBUG:".$winddirection_wholeday."\r\n";
	$winddirection_all = explode('#',substr($winddirection_wholeday,0,-1));
	#$out .= "DEBUG:".json_encode($winddirection_all)."\r\n";
	array_walk ( $winddirection_all, 'Compass\compass2degrees_inplace' );
	#$out .= "DEBUG:".json_encode($winddirection_all)."\r\n";
	$winddirection_avg = Circular\degrees_circular_avg($winddirection_all);
	$out .= "DEBUG:".$winddirection_avg."\r\n";
	$windicon = Compass\arrow(Compass\opposite($winddirection_avg));

	$out .= "SUMMARY: {$weather_icon}  {$temp_min}~{$temp_max}℃  {$beaufort}{$windicon}  {$precipitation}mm {$moon_icon}\r\n";	
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

/**
 * @param integer $conditions
 * @return string 
 * @desc score the weather conditions
 */
function score_weather_conditions($conditions)
{
	if (strpos(strtolower($conditions),'sleet') !== false) {
		$score = 0;
	}
	elseif (strpos(strtolower($conditions),'shower') !== false) {
		$score = 1;
	}
	elseif (strpos(strtolower($conditions),'rain') !== false) {
		$score = 2;
	}
	elseif (strpos(strtolower($conditions),'cloudy') !== false) {
		$score = 3;
	}
	elseif (strpos(strtolower($conditions),'fair') !== false) {
		$score = 4;
	}
	elseif (strpos(strtolower($conditions),'sunny') !== false) {
		$score = 5;
	}
	elseif (strpos(strtolower($conditions),'clear sky') !== false) {
		$score = 5;
	}
	else {
		$score = 9999;
	}
	return $score;
}

/**
 * @param integer $temperature
 * @param integer $condition_score
 * @return string 
 * @desc get a weather emoji
 * a few alternative icons: ☀️  🌞  🌦️ 🌥️ ⛅ 🌤️ 🌩️ ⛈️
 */
function weather_icon($temperature,$condition_score)
{
	if ($temperature > -1)
	{
		switch ($condition_score)
		{
			case 0:
				$weather_icon = '🌨️';
				break;
			case 1:
				$weather_icon = '🚿';
				break;
			case 2:
				$weather_icon = '☔';
				break;
			case 3:
				$weather_icon = '☁️';
				break;
			case 4:
				$weather_icon = '⛅';;
				break;
			case 5:
				$weather_icon = '😎';
				break;
			default:
				$weather_icon = '❓';
				break;
		}
	} else {
		switch ($condition_score)
		{
			case 0:
				$weather_icon = '🌨️';
				break;
			case 1:
				$weather_icon = '❄️';
				break;
			case 2:
				$weather_icon = '❄️';
				break;
			case 3:
				$weather_icon = '⛄️';
				break;
			case 4:
				$weather_icon = '⛄';;
				break;
			case 5:
				$weather_icon = '⛄';
				break;
			default:
				$weather_icon = '❓';
				break;
		}
	}
	return $weather_icon;
}
