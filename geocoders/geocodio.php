<?php

// Add support for Geocod.io to Geocoder for GravityForms

add_filter( 'gfg_geocoders_fields', 'gfg_geocodio_fields' );
add_filter( 'gfg_geocoders', 'gfg_geocodio_geocoders' );
add_filter( 'gfg_geocoder_keys', 'gfg_geocodio_keys', 10, 3 );

function gfg_geocodio_fields( $fields ) {
	$fields['Geocod.io full address'] = array(
		'street'		=> 'Street',
		'city'			=> 'City',
		'state'			=> 'State',
		'postal_code'	=> 'Postal Code',
		'country'		=> 'Country' 
	);

	$fields['Geocod.io simple query'] = array(
		'q'				=> 'Search Field'
	);

	return $fields;
}

function gfg_geocodio_geocoders( $geocoders ) {
	$geocoders[] = array(
		'name' => 'geocodio_key',
		'label' => 'Geocod.io API Key',
		'type' => 'text',
		'class' => 'small',
		'placeholder' => 'Geocod.io API Key',
		'geocoder' => 'Geocod.io simple query',
		'geocoder_engine' => 'geocodio',
	);

	$geocoders[] = array(
		'name' => 'geocodio_key',
		'geocoder' => 'Geocod.io full address',
		'geocoder_engine' => 'geocodio',
	);

	return $geocoders;
}

function gfg_geocodio_keys( $keys, $geocoding_engine, $form ) {
	if ( 'geocodio' !== $geocoding_engine ) {
		return $keys;
	}

	// Since the geocodio engine is in use, enqueue the file
	$base_url = plugins_url( '', dirname( __FILE__ ) );
	wp_enqueue_script( 'geocoder_geocodio', $base_url . '/geocoders/geocoder_geocodio.js', array( 'gfg_geocode' ), filemtime( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'geocoder_geocodio.js' ) );

	$gfg = Geocoder_for_Gravity::get_instance();
	$settings = $gfg->get_plugin_settings();

	$keys['api_key'] = $settings[ 'geocodio_key' ];


	return $keys;
}
