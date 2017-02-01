<?php
/*
Plugin Name: Geocoder for Gravity Forms
Plugin URI: http://cimbura.com
Description: Geocoder for Gravity Forms
Version: 0.0.1
Author: Michael Moore / Cimbura.com
Text Domain: cimburacom
Domain Path: /languages

-------------------------------------------------------------
Copyright 2016, Cimbura.com
*/

require_once( __DIR__ . '/lib/class-geocoder-gravity-settings.php' );
require_once( __DIR__ . '/lib/class-geocoder-gravity-field.php' );
require_once( __DIR__ . '/lib/wp-geometa-lib/wp-geometa-lib-loader.php' );

function geocoder_for_gf_init() {
	Geocoder_for_Gravity::get_instance();
}


define('GFG_VERSION','0.0.1');
add_action( 'gform_loaded', 'geocoder_for_gf_init', 5 );
