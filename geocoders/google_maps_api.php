<?php

// Add support for Google Maps API geocoding
//
add_filter( 'gfg_geocoders_fields', 'gfg_gmapi_fields' );
add_filter( 'gfg_geocoders', 'gfg_gmapi_geocoders' );
add_filter( 'gfg_geocoder_keys', 'gfg_gmapi_keys', 10, 3 );

function gfg_gmapi_fields( $fields ) {

	$fields['Google Maps API'] = array(
		'address' => 'Search Field',
	);

	return $fields;
}

function gfg_gmapi_geocoders( $geocoders ) {

	$geocoders[] = array(
		'name' => 'google_maps_key',
		'label' => 'Google Maps API Key', 
		'type' => 'text',
		'class' => 'small',
		'placeholder' => 'Google Maps API Key',
		'geocoder' => 'Google Maps API',
		'geocoder_engine' => 'google_maps_api',
	);

	return $geocoders;
}

function gfg_gmapi_keys( $keys, $geocoding_engine, $form ) {
	if ( 'google_maps_api' !== $geocoding_engine ) {
		return $keys;
	}

	// Since the geocodio engine is in use, enqueue the file
	$base_url = plugins_url( '', dirname( __FILE__ ) );
	wp_enqueue_script( 'geocoder_geocodio', $base_url . '/geocoders/geocoder_google_maps_api.js', array( 'gfg_geocode' ), filemtime( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'geocoder_google_maps_api.js' ) );

	$gfg = Geocoder_for_Gravity::get_instance();
	$settings = $gfg->get_plugin_settings();

	$keys['key'] = $settings[ 'google_maps_key' ];

	return $keys;
}
