Original project at:

https://github.com/allanlaal/weather-calendar-feed

# Main changes to the code

Great stuff :-)  Now I can import weather forecasts in Orage, calcurse and Outlook.
I've made a few changes.

Quick overview:

- using beaufort for wind speed, so requiring windspeed.php
- mv index.php get.php
- replaced and added a few weather emoji for the summary
- event availability is now "free" (don't show as busy)
- using the "Conditions" to determine precipitation_icon when temp_min>0
- choice between one weather icon (calculates averages) or multiple emoji using a URL param
- moonphase icons and short description (using external lib)
- wind direction as emoji

## Details (in Dutch)

Wijzigingen:

- windspeed.php toegevoegd
- rapportage in Beaufort ipv m/s
- iets andere VCARD SUMMARY (minder spartaans)
- mv index.php get.php
- emoji vervangen door betere
- ook bewolking toestaan ⛅
- availability: free

Evert, 2019-01-08

Later toegevoegd:

- maanstanden
- windrichting
- nettere code met namespaces

Evert, 2019-01-16
