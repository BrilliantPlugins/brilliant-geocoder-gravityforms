<?php
/**
 * Plugin Name: Brilliant Geocoder for Gravity Forms
 * Plugin URI: http://luminfire.com
 * Description: This Brilliant Geocoder for Gravity Forms lets you get map coordinates based on the contents of other fields in your form.
 * Version: 0.0.3
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

define( 'GFG_VERSION','0.0.3' );

/**
 * Set up the geocoder when GravityForms loads.
 */
function geocoder_for_gf_init() {
	require_once( __DIR__ . '/lib/class-geocoder-gravity-settings.php' );
	require_once( __DIR__ . '/lib/class-geocoder-gravity-field.php' );


	$wpgm_loader = __DIR__ . '/lib/wp-geometa-lib/wp-geometa-lib-loader.php';
	if ( !file_exists( $wpgm_loader ) ) {
		error_log( __( "Could not load wp-geometa-lib. You probably cloned wp-geometa from git and didn't check out submodules!", 'brilliant-geocoder-gravityforms' ) );
		return false;
	} 

	$leaflet_loader = __DIR__ . '/lib/leaflet-php/leaflet-php-loader.php';
	if ( !file_exists( $leaflet_loader ) ) {
		error_log( __( "Could not load Leaflet-PHP. You probably cloned wp-geometa from git and didn't check out submodules!", 'brilliant-geocoder-gravityforms' ) );
		return false;
	} 

	require_once( __DIR__ . '/lib/wp-geometa-lib/wp-geometa-lib-loader.php' );
	require_once( __DIR__ . '/lib/leaflet-php/leaflet-php-loader.php' );

	require_once( __DIR__ . '/geocoders/geocodio.php' );
	require_once( __DIR__ . '/geocoders/google_maps_api.php' );

	GFForms::include_addon_framework();
	Geocoder_for_Gravity::get_instance();
}

add_action( 'gform_loaded', 'geocoder_for_gf_init', 5 );


/**
 * On activation make sure that Gravity Forms is present. 
 */
function brilliant_geocoder_for_gravity_forms_activation_hook() {
	if ( !class_exists( 'GFForms' ) || -1 === version_compare( GFForms::$version, '2.0.0' ) ) {
		wp_die( esc_html__( 'This plugin requires Gravity Forms 2.0.0 or higher. Please install and activate it first, then activate this plugin.', 'brilliant-geocoder-gravityforms') );
    }

	$wpgm_loader = __DIR__ . '/lib/wp-geometa-lib/wp-geometa-lib-loader.php';
	if ( !file_exists( $wpgm_loader ) ) {
		wp_die( esc_html__( "Could not load wp-geometa-lib. You probably cloned wp-geometa from git and didn't check out submodules!", 'brilliant-geocoder-gravityforms' ) );
	}

	$leaflet_loader = __DIR__ . '/lib/leaflet-php/leaflet-php-loader.php';
	if ( !file_exists( $leaflet_loader ) ) {
		wp_die( esc_html__( "Could not load Leaflet-PHP. You probably cloned wp-geometa from git and didn't check out submodules!", 'brilliant-geocoder-gravityforms' ) );
	}

	require_once( $wpgm_loader );
	WP_GeoMeta::install();
}

register_activation_hook( __FILE__ , 'brilliant_geocoder_for_gravity_forms_activation_hook' );
