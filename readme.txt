=== Brilliant Geocoder for Gravity Forms ===
Contributors: stuporglue, luminfire, cimburacom
Tags: Gravity Forms, GIS, geo, Spatial, geocoding, WP-GeoMeta, OSM, Google
Maps API, map, GeoJSON
Tested up to: 4.7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Capture location information in Gravity Forms by geocoding user's input into other form fields. 

== Description ==

Brilliant Geocoder for Gravity Forms is a powerful and flexible geocoder field
for Gravity Forms. The Geocoder field is easily be configured to capture geocoder 
input values from other form fields.

It comes with the OSM Nominatim geocoder enabled by default and supports Geocod.io 
and the Google Maps API once you've entered API keys for those services. 

*NOTICE*: OSM Nominatim requests that you include your email address in API calls 
if you are making a large number of requests, so we send the WP admin email address 
by default. You can change what is sent in the Geocoder settings in Gravity
Forms.

The geocoder field can be displayed as a map, as latitude and longitude
fields, as the raw GeoJSON data, or hidden.

This plugin supports WP-GeoMeta, so if you create posts or users with geocoded
data, their location will be stored as spatial metadata.


== Installation ==

Be sure that Gravity Forms is installed. 

With Gravity Forms installed, you can install this plugin in the usual WordPress way.

1. Upload the plugin files to the `/wp-content/plugins/geometa-acf` directory,
    or install the plugin through the WordPress plugin screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Optionally visit the Gravity Forms settings page enter a Geocod.io or
Google Maps API key, or to change which email is sent with OSM Nominatim API calls.

Create a new Gravity Form and add the Geocoder field from the Advanced Fields
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

