# weather-calendar-feed
Display yr.no weather forecasts in Google Calendar with a compact title and very detailed description.

## usage

Example calendar URL:

	http://weather.is.not.permanent.ee/?location=Estonia/Harjumaa/Tallinn&filename=weather.ics

Modify the parameters in the URL according to your needs. You will atleast need to modify latitude and longitude.

Add the above url into your Google Calendar at Other Calendars -> down arrow box thingie -> Add by URL

### location
Look up your location in yr.no and copy it from the URL bar

	location=Estonia/Harjumaa/Tallinn

### filename
This is the filename of the file offered for download when you access the calendar URL directly. This needs to be the last URL parameter, because otherwise Google Calendar will not read anything from this URL and will silently fail.

	filename=weather.ics



## Questions & Answers
Q: Why yr.no?
A: Because it has the most accurate weather forecast system that I know of.

Q: How many days in advance is the forecast?
A: 10 days.

Q: Can I get more days advanced forecast?
A: No. Maybe if Norway finds more oil fields. Yr.no is their governments national weather forecast service.

Q: Why only metric units?
A: If you are still using the length of some kings foot as the basis of your system of measurement, please consider converting to SI :)