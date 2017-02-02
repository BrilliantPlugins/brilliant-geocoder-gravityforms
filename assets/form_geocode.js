// These are the geocodings that can occur for the forms on the current page.
window.gfg_geocodings = window.gfg_geocodings || {};

// This is the function that collects data and calls the geocoder
function gfg_update_geocoder( e ) {

	// A failure handler. It just clears the field.
	gfg_update_geocoder.handle_failure = gfg_update_geocoder.handle_failure || function( failure ){
		console.log( failure );
		jQuery('#' + results_field ).val('');
	};

	// Find out which geocoders need to be re-populated. 
	// More than one geocoder might depend on the same input field, maybe.
	var update_me = [];
	for ( var gc in gfg_geocodings ) {
		if ( gfg_geocodings[gc].fields[ e.target.id ] !== undefined ) {
			update_me.push( gc );
		} 
	}

	var curgc; // current geocoder
	var target_geocode_field; // the results field
	var fields; // dict of all source IDs and their cooresponding keys in the geocode request
	var args; // The dict of args we'll pass to the geocoder
	var attr; // iterator variable to loop through fields. Will hold source field IDs.
	var res; // results of geocoding

	for ( var q = 0; q < update_me.length; q++ ) {
		target_geocode_field = update_me[ q ];
		curgc = gfg_geocodings[ target_geocode_field ];    
		args = {};

		// Fetch user-input data
		for ( attr in curgc.fields ) {
			args[ curgc.fields[ attr ] ] = jQuery('#' + attr).val();
		} 

		// Append server-provided data (API keys, etc.)
		if ( gfg_geocoder_keys[ curgc.engine ] !== undefined ) {
			args = jQuery.extend( args, gfg_geocoder_keys[ curgc.engine ] );
		}

		// Get a promise back (hopefully!)
		res = gfg_geocoder_engines[curgc.engine]( args, target_geocode_field );

		// Set up a failure handler
		if (typeof value === 'object' && typeof value.fail === 'function') {
			res.fail( gfg_update_geocoder.handle_failure );
		}
	}
}

// These are the functions that call the geocoders and process responses
window.gfg_geocoder_engines = {
	'nomination' : function( args, results_field ) {

		// Nomination doesn't like newlines in the query
		if ( typeof args.q !== undefined ) {
			args.q = args.q.replace(/\n/g,",");
		}

		/**
		 * Do a get request, and handle success. 
		 *
		 * Also, return the jQuery.get() result, which will be a deferred/promise.
		 */
		return jQuery.get('https://nominatim.openstreetmap.org/search?' + jQuery.param( args ), function( success ){

			var geojson = '';

			// On success, build a GeoJSON object
			if ( success.length > 0 ) {
				var res = success[0];

				geojson = {
					'type': 'Feature',

					'geometry': {
						'type': 'Point',
						'coordinates': [
							parseFloat(res.lon),
							parseFloat(res.lat)
						],
					}
				};

				delete res.boundingbox;
				var props = res.extratags || {};
				delete res.extratags;
				delete res.lat;
				delete res.lon;

				props = jQuery.extend( props, res );

				geojson.properties = props;
			};

			if ( geojson === '' ) {
				jQuery('#' + results_field ).val('');
			} else {
				// Set the result
				jQuery('#' + results_field ).val( JSON.stringify( geojson ) );
			}
		});
	}
};

// This holds any extra values that the geocoder will need such as API keys
window.gfg_geocoder_keys = window.gfg_geocoder_keys || {};
