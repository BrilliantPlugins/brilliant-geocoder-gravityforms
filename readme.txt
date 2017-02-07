=== Brilliant Geocoder for Gravity Forms ===
Contributors: stuporglue, luminfire, cimburacom
Tags: Gravity Forms, GIS, geo, Spatial, geocoding, WP-GeoMeta, OSM, Nominatim, Google, Maps API, map, GeoJSON
Tested up to: 4.7.2
Requires at least: 4.4.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Capture location information in Gravity Forms by geocoding user's input into other form fields. 

== Description ==

Brilliant Geocoder for Gravity Forms is a powerful and flexible geocoder field
for Gravity Forms. The Geocoder field is easily be configured to capture geocoder 
input values from other form fields.

It comes with the [OSM
Nominatim](http://wiki.openstreetmap.org/wiki/Nominatim)
geocoder enabled by default and supports [Geocod.io](https://geocod.io/)
and the [Google Maps API](https://developers.google.com/maps/) once you've entered 
API keys for those services. 

The geocoder field can be displayed as a map, as latitude and longitude
fields, as the raw GeoJSON data, or hidden.

This plugin supports WP-GeoMeta, so if you create posts or users with geocoded
data, their location will be stored as spatial metadata.

**NOTICE**: _OSM Nominatim requests that you include your email address in API calls 
if you are making a large number of requests, so we send the WP admin email address 
by default. You can change what is sent on the Gravity Forms settings page,
under *Geocoder*_.

= What is Geocoding? =

[Geocoding](https://en.wikipedia.org/wiki/Geocoding) is the process of turning 
text (an address) into coordinates (latitude and longitude). With coordinates 
you can display data on a map and do location based searching.

= Support for Other Geocoders =

Brilliant Geocoders for Gravity Forms includes hooks so that you can add
support for other geocoding services. 

OSM Nominatim support is built into the plugin, but Geocod.io and Google Maps
API support is written the same way that you would add support for another
service. The Geocod.io example in particular has extensive comments. 

Please see geocoders/geocodio.php and geocoders/geocodio.js for details.


== Installation ==

Be sure that Gravity Forms is installed. 

With Gravity Forms installed, you can install this plugin in the usual WordPress way.

1. Unzip and upload the plugin files to the `/wp-content/plugins/brilliant-geocoder-gravityforms` directory,
    or upload the plugin's .zip file through the WordPress plugin screen directly.
2. Activate the plugin on the 'Plugins' screen in WordPress.

= Creating your first Geocoding field = 

1. (Optional) Visit the Gravity Forms settings page enter a Geocod.io or
Google Maps API key, or to change which email is sent with OSM Nominatim API calls.
2. Create a new Gravity Form (or edit an existing one).
3. (Optional) Visit the form settings page and select which geocoder to use.
It will use OSM Nominatim by default.
4. Add the input fields you want the user to fill out.
5. Add the Geocoder field (under the Advanced Fields tab).
6. In the Geocoder field associate the geocoder parameters with the other input
fields on your form. 
7. Publish your form and add it to a page like you would any other Gravity
Form!

and add the Geocoder field from the Advanced Fields
menu. Add other input fields, then in the Geocoder settings select which
fields will be used as parameters for the geocoding.


== Frequently Asked Questions ==

No one has actually asked any questions yet!

= How can I run spatial queries? =

If you create users or posts, and set a meta value to the value of a  Brilliant 
Geocoder field, that value will be stored as spatial metadata. 

Brilliant Geocoder for Gravity Forms uses WP-GeoMeta internally. For sample 
queries, please see the [WP-GeoMeta documentation](https://github.com/cimburadotcom/wp-geometa#querying).

= Where can I get help with GIS and WordPress? = 

For commercial support you can contact the plugin developer at
[Cimbura.com](https://cimbura.com/contact-us/project-request-form/)

For fast and short questions you can [contact
me](https://twitter.com/stuporglue) on twitter.

== Changelog ==

= 0.0.1 = 
* Initial release!
* Support for OSM Nominatin
* Support for Google Maps API
* Support for Geocod.io
* Forward geocoding support
* Admin value editing

== Upgrade Notice ==
= 0.0.1 = 
* You don't have Brilliant Geocoder for Gravity Forms yet, so there's no need to read this upgrade
notice!

