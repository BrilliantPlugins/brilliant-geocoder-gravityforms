window.gfg_geocoder_engines.google_maps_api = function( args, results_field ) {

	// Make an API call and return a promise.
	return jQuery.get('https://maps.googleapis.com/maps/api/geocode/json?' + jQuery.param( args ), function( success ) {

		if ( success.status !== 'OK' ) {
			jQuery('#' + results_field ).val('');
			return;
		} 

		var geojson = '';

		if ( success.results.length > 0 ) {
			var res = success.results[0];

			// Build a GeoJSON object.
			geojson = {
				'type': 'Feature',

				'geometry': {
					'type' : 'Point',
					'coordinates' : [
						res.geometry.location.lng,
						res.geometry.location.lat
					]
				},
				'properties' : {
					'formatted_address': res.formatted_address,
					'types': res.types,
					'location_type': res.location_type,
					'place_id': res.place_id
				}
			};
		}

		if ( geojson === '' ) {
			jQuery('#' + results_field ).val('');
		} else {

			// Set the result
			jQuery('#' + results_field ).val( JSON.stringify( geojson ) );
		}
	});
};
