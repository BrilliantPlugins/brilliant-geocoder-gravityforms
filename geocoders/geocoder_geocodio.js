window.gfg_geocoder_engines = window.gfg_geocoder_engines || {};

window.gfg_geocoder_engines.geocodio = function( args, results_field ) {
	var promise = jQuery.get("https://api.geocod.io/v1/geocode?" + jQuery.param( args ));

		promise.then( function( success ) {
			console.log( success );

			var geojson = '';

			if ( success.results.length > 0 ) {
				var res = success.results[0];
				if ( res.accuracy <= 5 )  {
					var geojson = {
						'type': 'Feature',

						'geometry': {
							'type' : 'Point',
							'coordinates' : [
								res.location.lng,
								res.location.lat
							]
						},
						'properties' : jQuery.extend(res.address_components, {
							'accuracy' : res.accuracy,
							'accuracy_type' : res.accuracy_type,
							'source' : res.source
						})
					};
				}
			}

			if ( geojson === '' ) {
				jQuery('#' + results_field ).val('');
			} else {
				jQuery('#' + results_field ).val( JSON.stringify( geojson ) );
			}

		},
		function( failure ) {
			jQuery('#' + results_field ).val('');
		});
};
