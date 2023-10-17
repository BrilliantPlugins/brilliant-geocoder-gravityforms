Brilliant Geocoder for Gravity Forms
==========================
![Geocoder for Gravity Forms lets you get location information from form values.](assets/banner-1544x500.png)

Brilliant Geocoder for Gravity Forms is a powerful and flexible geocoder field
for Gravity Forms. The Geocoder field is easily be configured to capture geocoder
input values from other form fields.

It comes with the [OSM Nominatim](http://wiki.openstreetmap.org/wiki/Nominatim)
geocoder enabled by default and supports [Geocod.io](https://geocod.io/)
and the [Google Maps API](https://developers.google.com/maps/) once you've entered
API keys for those services.

The geocoder field can be displayed as a map, as latitude and longitude
fields, as the raw GeoJSON data, or hidden.

This plugin supports WP-GeoMeta, so if you create posts or users with geocoded
data, their location will be stored as spatial metadata.

**NOTICE**: _This plugin uses 3rd party services to provide geocoding results.
The default geocoder, OSM Nominatim, requests that you include your email address
in API calls if you are making a large number of requests. We send the WP admin
email address by default. You can change what is sent on the Gravity Forms settings
page, under *Geocoder*._.

What is Geocoding?
------------------

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


Screenshots
-----------
Brilliant Geocoder for Gravity Forms watches for changes to the form, then finds a location based on those fields.

![Brilliant Geocoder for Gravity Forms geocoding an address.](assets/screenshot-1.png)

Brilliant Geocoder for Gravity Forms comes with support for three geocoders. To use Geocod.io or Google Maps API,
you will need to visit the Gravity Forms settings and enter your API keys.

![The Brilliant Geocoder for Gravity Forms settings page.](assets/screenshot-2.png)

On individual form settings pages you can select which geocoder to use for the current form. Only geocoders with required API
key will appear in this list.

![The Brilliant Geocoder for Gravity Forms individual form settings page.](assets/screenshot-3.png)

The fields that appear in the _Geocoding Source Fields_ section will dependo on which geocoder engine you've selected
on the form's settings page.

The geocoder fields you associate with source fields will be sent to the geocoder service. In this screenshot only a single field is needed.

![Configuring Brilliant Geocoder for Gravity Forms for use with a simple query geocoder.](assets/screenshot-4.png)

Other geocoding engines may need separate street, city and other fields to geocode correctly.

![Configuring Brilliant Geocoder for Gravity Forms for a more complex geocoding query.](assets/screenshot-5.png)

You have a lot of control over what you display to the user. The default is to display a map which will show the geocoded location with a marker.
You can also choose to show a GeoJSON Text area (which may only be for advanced users) or a pair of latitude and longitude text fields.

![Configuring Brilliant Geocoder for Gravity Forms ](assets/screenshot-6.png)

Here's what the three inputs will look like to the user. The map is at the top, then the GeoJSON, then the Latitude and Longitude.

![Brilliant Geocoder for Gravity Forms showing all three user view options at once.](assets/screenshot-7.png)

Alternatively you could hide the geocoding interface from the user completely. It will still work and submit correctly, it just won't be visible.

![You can hide Brilliant Geocoder for Gravity Forms if you want to.](assets/screenshot-8.png)

Geocoded results are stored as GeoJSON but, for convenience, just the coordinates are show on the entry listing page.

![Brilliant Geocoder for Gravity Forms keeps the entry page tidy.](assets/screenshot-9.png)

On the full entry view page the map and the GeoJSON are shown.

![Brilliant Geocoder for Gravity Forms shows a map on the entry view page.](assets/screenshot-10.png)

When editing existing form submissions, the map, GeoJSON and latitude and longitude fields are all interlinked. You can change any of them and the other two will update. You can also edit the associated input fields and the geocoding will be updated as you would expect.

![Brilliant Geocoder for Gravity Forms tries to make editing existing entries a brilliant experience too.](assets/screenshot-11.png)


Installation
------------

Be sure that Gravity Forms 2.0.0 or higher is installed.

With Gravity Forms installed, you can install this plugin in the usual WordPress way.

1. Unzip and upload the plugin files to the `/wp-content/plugins/brilliant-geocoder-gravityforms` directory,
    or upload the plugin's .zip file through the WordPress plugin screen directly.
2. Activate the plugin on the 'Plugins' screen in WordPress.

### Creating your first Geocoding field

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


Frequently Asked Questions
--------------------------

### Where are the Terms of Service for the Geocoding Services?
 - [Google Maps API Terms of Service](https://developers.google.com/maps/terms)
 - [Geocod.io Terms of Use](https://geocod.io/terms-of-use/)
 - [OSM Nominatim Usage Policy](https://developers.google.com/maps/terms)

### What data is sent to the geocoding service when I geocode?

Whatever fields you select as a geocoding source in Gravity Forms will be sent
to the geocoding service. This would typically means that address details
entered into the form will be sent to the geocoding service for processing.

Your API key and/or any other parameters required by the service's terms of service
will also be sent.

Please review the Terms of Service of the service you select for details on
how your submitted data is stored or used.

### How can I run spatial queries?

If you create users or posts, and set a meta value to the value of a  Brilliant
Geocoder field, that value will be stored as spatial metadata.

Brilliant Geocoder for Gravity Forms uses WP-GeoMeta internally. For sample
queries, please see the [WP-GeoMeta documentation](https://github.com/cimburadotcom/wp-geometa#querying).

### Where can I get help with GIS and WordPress?

For commercial support you can contact the plugin developer at
[Cimbura.com](https://cimbura.com/contact-us/project-request-form/)

For fast and short questions you can [contact me](https://twitter.com/stuporglue) on twitter.

Changelog
---------

= 0.0.2 =
* Updated wp-geometa-lib.
* Updated readme documentation.
* Tested with WP 4.8
* Some additional debugging code.
* Fixed Google Geocoder.

= 0.0.1 =
* Initial release!
* Support for OSM Nominatim
* Support for Google Maps API
* Support for Geocod.io
* Forward geocoding support
* Admin value editing
