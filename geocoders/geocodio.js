/**
 * Set up the Geocod.io geocoder API call
 *
 * @param dict args All of the arguments that are going to get sent to Geocod.io.
 * @param func success_callback Function to pass geojson to.
 * @param func failure_callback Function to pass anything else to.
 *
 * On success you should be able to construct a GeoJSON object and pass it to the success_callback function.
 */
window.gfg_geocoder_engines.geocodio = function( args, success_callback, failure_callback) {

	/**
	 * A geocoder engine should make an API call and call the appropriate callback function, 
	 * either the success callback, or the failure callback.
	 *
	 * Here we turn the args dictionary into a query string and fetch the URL.
	 */
	jQuery.get('https://api.geocod.io/v1/geocode?' + jQuery.param( args ), function( success ) {

		/**
		 * Our success handler needs to turn a successful geocode result into GeoJSON.
		 *
		 * The GeoJSON should be a single Feature (not a FeatureCollection).
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
			failure_callback( success );
		} else {
			success_callback( geojson );
		}
	});
};
