/**
 * Set up the Geocod.io geocoder API call
 *
 * @param dict args All of the arguments that are going to get sent to Geocod.io.
 * @param string result_field The ID of the field where the results need to be sent. 
 */
window.gfg_geocoder_engines.geocodio = function( args, results_field ) {

	/**
	 * A geocoder engine should make an API call and return a promise. 
	 *
	 * Here we turn the args dictionary into a query string and fetch the URL.
	 * 
	 * We return jQuery.get(), which will be a promise. 
	 */
	return jQuery.get('https://api.geocod.io/v1/geocode?' + jQuery.param( args ), function( success ) {

		/**
		 * Our success handler needs to turn a successful geocode result into GeoJSON.
		 *
		 * This will allow results to be used on maps, and to be stored with WP-GeoMeta.
		 */

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

			// Finally, the geojson should be stringified and the results_field value set.
			jQuery('#' + results_field ).val( JSON.stringify( geojson ) );
		}
	});
};
