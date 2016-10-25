# weather-calendar-feed
Display yr.no weather forecasts in Google Calendar with a compact title and very detailed description.

## usage

	http://weather.is.not.permanent.ee/?location={Your/Weather/Location}&filename=weather.ics


Example calendar URL:

	http://weather.is.not.permanent.ee/?location=Estonia/Harjumaa/Tallinn&filename=weather.ics

Modify the location in the URL according to your needs.

Add the above url into your Google Calendar at Other Calendars -> down arrow box thingie -> Add by URL. 


### location
Look up your location in (Yr.no)[http://www.yr.no/soek/soek.aspx?spr=eng], go to that page and copy your Yr.no location string from the URL bar (everything after http://www.yr.no/place/)

	location=Estonia/Harjumaa/Tallinn

### filename
This is the filename of the file offered for download when you access the calendar URL directly. This needs to be the last URL parameter, because otherwise Google Calendar will not read anything from this URL and will silently fail.

	filename=weather.ics

If you already added this calendar and Google is having difficulties updating the weather feed, then import it again changing the filename parameter from weather.ics to some_other_random_name.ics

## Questions & Answers


**Q: Whats in the title of the calendar event?**

**A:** for example: 

	30|-22❄12㎜11.2⇶

which means: 

	{Highest temperature}|{Lowest Temperature}{Icon depicting todays weather}{Total rainfall in mm}㎜{max windspeed in m/s}



**Q: What does the little icon between todays temperature and rainfall show?**

**A:** This is the icon depicting todays weather: There are 4 different icons:

* ❄ = Temperature below 0°C with snow/rain
* ⛄ = Temperature below 0°C without any rain
* ☔ = Temperature above 0°C with rain
* ☀ = Temperature above 0°C without any rain



**Q: Why yr.no?**

**A:** Because it has the most accurate weather forecast system that I know of.



**Q: How many days in advance is the forecast?**

**A:** 10 days.



**Q: Can I get more days advanced forecast?**

**A:** No. Maybe if Norway finds more oil fields. Yr.no is their governments national weather forecast service and limits forecasts to 10 days.



**Q: Why only metric units?**

**A:** If you are still using the length of some kings foot as the basis of your system of measurement, please consider converting to SI :)
