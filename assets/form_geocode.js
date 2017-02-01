window.gfg_geocodings = window.gfg_geocodings || {};

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
	for ( var q = 0; q < update_me.length; q++ ) {
		target_geocode_field =     update_me[ q ];
		curgc = gfg_geocodings[ target_geocode_field ];    


	}
}

window.gfg_geocoder_engines = {
	'geocodio' : function( args ) {
		console.log( "Geocoding with geocodio" );
	},
	'nomination' : function( args ) {
		console.log( "Geocoding with nomination" );
	},
	'google_maps' : function( args ) {
		console.log( "Geocoding with google maps" );
	}
}
