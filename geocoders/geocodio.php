<?php

/**
 * This is an example of how to add support for other geocoders to Geocoder For Gravity Forms
 *
 * To add support for a new geocoder, you will need to implement three filters and add a single
 * javascript file.
 */

add_filter( 'gfg_geocoders_fields', 'gfg_geocodio_fields' );
/**
 * The gfg_geocoder_fields filter holds a mapping for a geocoder between the user input request parameters and the human readable labels for those parameters.
 *
 * Other parameters such as keys or other request options that users can't set should be configured in the gfg_geocoder_keys filter.
 *
 * As with all WordPress filters, return the $fields object.
 */
function gfg_geocodio_fields( $fields ) {

	/**
	 * The top-level key (Geocod.io full addres, in this case)
	 * is an admin-readable name for the geocoder.
	 *
	 * The admin will be able to map each of the keys in the array ( street,
	 * city, state, etc.) to another input in the form.
	 *
	 * The admin will see 'Stree', 'City', etc. which allows you to 
	 * map ambiguous query parameters to something an admin will know
	 * what to do with.
	 */
	$fields['Geocod.io full address'] = array(
		'street'		=> 'Street',
		'city'			=> 'City',
		'state'			=> 'State',
		'postal_code'	=> 'Postal Code',
		'country'		=> 'Country' 
	);

	/**
	 * Some geocoding services have multiple ways to run a 
	 * geocode request. In Geocod.io's case you can either 
	 * specify individual address parts, or just give them a query
	 * string and let them try to parse it.
	 *
	 * We can add support for both methods in this same callback.
	 * We'll just be sure to set the 'geocoder_engine' for both to
	 * 'geocodio' in the gfg_geocoders callback.
	 */
	$fields['Geocod.io simple query'] = array(
		'q'				=> 'Search Field'
	);

	return $fields;
}

/**
 * This filter does two things. 
 *
 * (1) It lets you populate the list of geocoders an admin can pick from. 
 * (2) It lets you add inputs for required keys or other info that a given geocoder needs. 
 */
add_filter( 'gfg_geocoders', 'gfg_geocodio_geocoders' );
function gfg_geocodio_geocoders( $geocoders ) {

	/**
	 * Each entry in the $geocoder array can be a plugin setting OR a geocoder type.
	 */


	/*
	 * For settings include at least the keys 'name','label', and 'type' to create a new setting. 
	 *
	 * For a full list of keys, see: 
	 * https://www.gravityhelp.com/documentation/article/gfaddon/#creating-plugin-settings
	 */
	$geocoders[] = array(
		'name' => 'geocodio_key',
		'label' => 'Geocod.io API Key',
		'type' => 'text',
		'placeholder' => 'Geocod.io API Key',
	);

	/**
	 * To add geocoders, include at least the keys 'geocoder' and 'geocoder_engine'. 
	 *
	 * The value of 'geocoder' should match up with one of the entries in the gfg_geocoder_fields 
	 * filter above. 
	 *
	 * The value of 'geocoder_engine' should match up to a JavaScript function
	 * in gfg_geocoder_engines
	 *
	 * Eg. window.gfg_geocoder_engines.geocodio in our case. Check out geocodio.js 
	 * to see how this is done.
	 */
	$geocoders[] = array(
		'geocoder' => 'Geocod.io full address',
		'geocoder_engine' => 'geocodio',
	);

	$geocoders[] = array(
		'geocoder' => 'Geocod.io simple query',
		'geocoder_engine' => 'geocodio',
	);

	return $geocoders;
}

/**
 * This filter is similar to gfg_geocoder_fields, except that the returned
 * array is meant for geocoder parameters that users can't/shouldn't change such 
 * as API keys.
 *
 * @param array $keys An array of existing keys.
 * @param string $geocoding_engine The name of the engine under consideration.
 * @param object $form The current form object
 */
add_filter( 'gfg_geocoder_keys', 'gfg_geocodio_keys', 10, 3 );
function gfg_geocodio_keys( $keys, $geocoding_engine, $form ) {

	// If the current geocoding engine isn't ours, just return $keys and be done.
	if ( 'geocodio' !== $geocoding_engine ) {
		return $keys;
	}

	/*
	 * Since the geocodio engine is in use, enqueue the geocodio.js file
	 *
	 * Make it depend on gfg_geocode to ensure that the needed JS objects exist
	 *
	 * I'm using the file modified timestamp as the version number, but you could use any value you want.
	 */
	$base_url = plugins_url( '', dirname( __FILE__ ) );
	wp_enqueue_script( 'geocoder_geocodio', $base_url . '/geocoders/geocodio.js', array( 'gfg_geocode' ), filemtime( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'geocodio.js' ) );

	// Fetch the GFG instance and get the settings.
	$gfg = Geocoder_for_Gravity::get_instance();
	$settings = $gfg->get_plugin_settings();

	// Set the api_key value so that we can make API calls.
	$keys['api_key'] = $settings[ 'geocodio_key' ];

	return $keys;
}
