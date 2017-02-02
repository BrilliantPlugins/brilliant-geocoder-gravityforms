### JavaScript Variables and Functions

 * gfg_geocoder_engines - A dict of functions where the keys are geocoding engine names and the functions handle the actual API calls and result processing.
 * gfg_geocodings - A dict of geocodings for the current form. The key is the target geocoding field. The values are information about which engine to use, which fields to use for source data, and what parameters to use for the request.
 * gfg_geocoder_keys - A dict of additional parameters to send to the geocoder service.

 * gfg_update_geocoder() - The event handler that is fired whenever a source input field is changed. 

### Filters and Actions

#### gfg_geocoders_fields
#### gfg_geocoders
#### gfg_geocoder_keys

Adding a new geocoder
---------------------

The Geocod.io geocoder has been implemented as an example geocoder with lots of code comments. 

Please see geocoders/geocodio.php and geocoders/geocoder_geocodio.js for an example of how to add a new geocoder.
