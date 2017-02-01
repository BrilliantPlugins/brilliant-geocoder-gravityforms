window.gfg_geocoder_engines = window.gfg_geocoder_engines || {};

window.gfg_geocoder_engines.geocodio = function( args, results_field ) {
	return jQuery.get('https://api.geocod.io/v1/geocode?' + jQuery.param( args ), function( success ) {

		var geojson = '';

		if ( success.results.length > 0 ) {
			var res = success.results[0];
			geojson = {
				'type': 'Feature',

				'geometry': {
					'type' : 'Point',
					'coordinates' : [
						parseFloat(res.location.lng),
						parseFloat(res.location.lat)
					]
				},
				'properties' : jQuery.extend(res.address_components, {
					'accuracy' : res.accuracy,
					'accuracy_type' : res.accuracy_type,
					'source' : res.source
				})
			};
		}

		if ( geojson === '' ) {
			jQuery('#' + results_field ).val('');
		} else {
			jQuery('#' + results_field ).val( JSON.stringify( geojson ) );
		}

	});
};
