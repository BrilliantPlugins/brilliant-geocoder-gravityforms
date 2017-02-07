<?php
/**
 * Plugin Name: Brilliant Geocoder for Gravity Forms
 * Plugin URI: http://luminfire.com
 * Description: This Brilliant Geocoder for Gravity Forms lets you get map coordinates based on the contents of other fields in your form.
 * Version: 0.0.1
 * Author: Michael Moore / Luminfire.com
 * Text Domain: luminfire
 * Domain Path: /lang
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package brilliant-geocoder-gravityforms
 *
 * -------------------------------------------------------------
 * Copyright 2016, LuminFire
 */

/**
 * The Brilliant Geocoder for Gravity Forms initialization file.
 *
 * Brilliant Geocoder for Gravity Forms makes it easy to capture location information based on user's
 * input. Several geocoders are supported out of the box and there's an easy api so that developers
 * can add support for other geocoders.
 */

require_once( __DIR__ . '/lib/class-geocoder-gravity-settings.php' );
require_once( __DIR__ . '/lib/class-geocoder-gravity-field.php' );
require_once( __DIR__ . '/lib/wp-geometa-lib/wp-geometa-lib-loader.php' );
require_once( __DIR__ . '/lib/leaflet-php/leaflet-php-loader.php' );

require_once( __DIR__ . '/geocoders/geocodio.php' );
require_once( __DIR__ . '/geocoders/google_maps_api.php' );

/**
 * Set up the geocoder when GravityForms loads.
 */
function geocoder_for_gf_init() {
	Geocoder_for_Gravity::get_instance();
}

define( 'GFG_VERSION','0.0.1' );
add_action( 'gform_loaded', 'geocoder_for_gf_init', 5 );
