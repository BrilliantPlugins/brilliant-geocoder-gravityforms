// These are the geocodings that can occur for the forms on the current page.
window.gfg_geocodings = window.gfg_geocodings || {};

// This is the function that collects data and calls the geocoder
function gfg_update_geocoder( e ) {
	var update_me = [];
	for ( var gc in gfg_geocodings ) {
		if ( gfg_geocodings[gc].fields[ e.target.id ] !== undefined ) {
			update_me.push( gc );
		} 
	}

	var curgc;
	var target_geocode_field;
	var fields;
	var f;
	var args;
	var attr;
	for ( var q = 0; q < update_me.length; q++ ) {
		target_geocode_field =     update_me[ q ];
		curgc = gfg_geocodings[ target_geocode_field ];    
		args = {};

		for ( attr in curgc.fields ) {
			args[ curgc.fields[ attr ] ] = jQuery('#' + attr).val();
		} 

		if ( gfg_geocoder_keys[ curgc.engine ] !== undefined ) {
			args = jQuery.extend( args, gfg_geocoder_keys[ curgc.engine ] );
		}

		gfg_geocoder_engines[curgc.engine]( args, target_geocode_field );
	}
}

// These are the functions that call the geocoders and process responses
window.gfg_geocoder_engines = {
	'nomination' : function( args ) {
		console.log( "Geocoding with nomination" );
	}
};

// This holds any extra values that the geocoder will need such as API keys
window.gfg_geocoder_keys = window.gfg_geocoder_keys || {};
