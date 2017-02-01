window.gfg_geocodings = window.gfg_geocodings || {};

function gfg_update_geocoder( e ) {
	var update_me = [];
	for ( var gc in gfg_geocodings ) {
		if ( gfg_geocodings[gc].fields.indexOf( e.target.id ) !== -1 ) {
			update_me.push( gc );
		} 
	}

	var curgc;
	for ( var q = 0; q < update_me.length; q++ ) {
		curgc = gfg_geocodings[ q ];	

		// Check if all required fields are present
		// if they are, then fire off the appropriate geocoder
	}
}
