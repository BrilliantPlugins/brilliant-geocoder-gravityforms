// These are the geocodings that can occur for the forms on the current page.
window.gfg_geocodings = window.gfg_geocodings || {};

// This is the function that collects data and calls the geocoder
function gfg_update_geocoder( e ) {

	// A failure handler. It just clears the field.
	gfg_update_geocoder.handle_failure = gfg_update_geocoder.handle_failure || function( failure ){
		jQuery('#' + target_geocode_field).val('').trigger('change');
	};

	// A success handler. It sets the field
	gfg_update_geocoder.handle_success = gfg_update_geocoder.handle_success || function( success ){

		if ( typeof success !== 'string' ) {
			success = JSON.stringify( success );
		}

		jQuery('#' + target_geocode_field).val(success);
		jQuery('#' + target_geocode_field).trigger('change');

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
			if ( args[ curgc.fields[ attr ] ] === undefined || args[ curgc.fields[ attr ] ] === '' ) {
				delete args[ curgc.fields[ attr ] ];
			}
		} 

		// Append server-provided data (API keys, etc.)
		if ( gfg_geocoder_keys[ curgc.engine ] !== undefined ) {
			args = jQuery.extend( args, gfg_geocoder_keys[ curgc.engine ] );
		}

		gfg_geocoder_engines[curgc.engine]( args, gfg_update_geocoder.handle_success, gfg_update_geocoder.handle_failure );
	}
}

// These are the functions that call the geocoders and process responses
window.gfg_geocoder_engines = {
	'nominatim' : function( args, success_callback, failure_callback ) {

		// Nominatim doesn't like newlines in the query
		if ( args.q !== undefined ) {
			args.q = args.q.replace(/\n/g,",");
		}

		/**
		* Do a get request, and handle success. 
		*
		* Also, return the jQuery.get() result, which will be a deferred/promise.
		*/
		jQuery.get('https://nominatim.openstreetmap.org/search?' + jQuery.param( args ), function( success ){

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
				failure_callback( success );
			} else {
				success_callback( geojson );
			}
		});
	}
};

// This holds any extra values that the geocoder will need such as API keys
window.gfg_geocoder_keys = window.gfg_geocoder_keys || {};

// This handles syncing info between the map, the geojson input and the lat/lng fields
// field_id is the main target field ID - ie. the geojson field
window.gfg_sync_data = function( field_id ){

	var self = this;

	this.init = function(){
		this.field_id = field_id;
		this.mapobj = window['geocode_map_' + field_id];
		this.lat = jQuery('#' + field_id + '_lat');
		this.lng = jQuery('#' + field_id + '_lng');
		this.geojson = jQuery('#' + field_id);

		if ( this.mapobj !== undefined && L.Draw !== undefined ) {
			this.mapobj.map.on( L.Draw.Event.EDITED, this.sync_everything);
			this.mapobj.map.on( L.Draw.Event.CREATED, this.sync_everything);
			this.mapobj.map.on( L.Draw.Event.DELETED, this.sync_everything);
		} else {
			this.mapobj = {'map':-1};
		}

		if ( this.lat !== undefined && this.lng !== undefined ) {
			this.lat.on('change',this.sync_everything);
			this.lng.on('change',this.sync_everything);
		} else {
			this.lat = [-1];
			this.lng = [-1];
		}

		if ( this.geojson !== undefined ) {
			this.geojson.on('change',this.sync_everything);
		}
	};


	this.sync_everything = function(e){

		var new_geojson;

		switch ( e.target ) {
			case self.lat[0]:
			case self.lng[0]:

				if ( NaN !== parseFloat(self.lat.val()) && NaN !== parseFloat(self.lng.val()) ) {
					new_geojson = {
						"type":"Feature",
						"geometry":{
							"type":"Point",
							"coordinates":[ parseFloat(self.lng.val()), parseFloat(self.lat.val()) ]
						},
						"properties":{}
					};
				}
				break;
			case self.geojson[0]:
				try {
					new_geojson = JSON.parse( self.geojson.val() );
				} catch (e){
					// do nothing
				}
				break;
			case self.mapobj.map:
				new_geojson = self.mapobj.layers.editthis.toGeoJSON();

				// On new layer creation, capture the new layer instead of the old one.
				if ( e.layer !== undefined ) {
					new_geojson = e.layer.toGeoJSON();
				}

				jQuery(new_geojson.features).each(function(){this.properties = {};});
				break;
		}

		if ( new_geojson !== undefined && new_geojson.type === 'FeatureCollection' ) {
			new_geojson = new_geojson.features[0];
		}

		if ( 
			undefined !== new_geojson && 
			undefined !== new_geojson.geometry && 
			undefined !== new_geojson.geometry.coordinates && 
			2 === new_geojson.geometry.coordinates.length
		) {
			if ( typeof self.lat.val === 'function' && typeof self.lng.val === 'function' ) {
				self.lat.val(new_geojson.geometry.coordinates[1]);
				self.lng.val(new_geojson.geometry.coordinates[0]);
			}
			if ( typeof self.geojson.val === 'function' ){
				self.geojson.val(JSON.stringify(new_geojson));
			}
			if ( self.mapobj.layers !== undefined &&
			self.mapobj.layers.editthis !== undefined ) {
				self.mapobj.layers.editthis.clearLayers(); 
				self.mapobj.layers.editthis.addData( new_geojson );
			}
		} else {
			if ( typeof self.lat.val === 'function' && typeof self.lng.val === 'function' ) {
				self.lat.val('');
				self.lng.val('');
			}
			if ( typeof self.geojson.val === 'function' ){
				self.geojson.val('');
			}
			if ( self.mapobj.layers !== undefined &&
			self.mapobj.layers.editthis !== undefined ) {
				self.mapobj.layers.editthis.clearLayers(); 
			}
		}

		if ( e.target !== self.geojson[0] ) {
			if ( typeof self.geojson.trigger === 'function' ) {
				self.geojson.trigger("change");
			}
		}
	};

	this.init();
};
