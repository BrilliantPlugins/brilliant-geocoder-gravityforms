window.gfg_geocoder_engines.google_maps_api = function( args, success_callback, failure_callback ) {
	var params = {};
	var address = [];
	var key = '';

	// Split the address and the key into their own parameters
	jQuery.each(args, function( index, value ){
		if ( index != 'key' ) {
			address.push(value);
		} else {
			key = value;
		}
	})

	address = address.join('+');
	params.address = address;
	params.key = key

	// Make an API call and call the appropriate callback function.
	jQuery.get('https://maps.googleapis.com/maps/api/geocode/json?' + jQuery.param( params ), function( success ) {

		if ( success.status !== 'OK' ) {
			failure_callback( success );
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
			failure_callback( success );
		} else {
			success_callback( geojson );
		}
	});
};

