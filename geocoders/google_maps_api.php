<?php

// Add support for Google Maps API geocoding
//
add_filter( 'gfg_geocoders_fields', 'gfg_gmapi_fields' );
add_filter( 'gfg_geocoders', 'gfg_gmapi_geocoders' );

function gfg_gmapi_fields( $fields ) {

	$fields['Google Maps API'] = array(
		'q' => 'Search Field',
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
