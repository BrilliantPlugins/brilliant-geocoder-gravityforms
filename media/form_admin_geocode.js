/**
* On the admin page, each time a geocoder field is edited, set up the mapping for it.
*/
jQuery(document).bind('gform_load_field_settings', function(e, field, form){

	if ( 'geocoder' !== field.type ) {
		return;
	}

	var fields = gfg_geocoders[ form.which_geocoder || 'OSM Nominatim simple query' ];

	var ruleFields;
	var html = '';
	for( var i in fields ) {

		html += '<tr class="default_input_value_row" id="geocoding_source_wrap_' + i + '">';
		html += '<td><label for="geocoding_source_' + i + '" class="inline">' + fields[i] + '</label></td>';

		// Get the rule fields, then unset the onchange attribute
		ruleFields = jQuery( GetRuleFields('geocoding', i, '' ) );
		ruleFields.find('option[value="' + field.id + '"]').remove(); // Remove ourself from the list.
		ruleFields.prepend( '<option value="">Don\'t geocode with this field</option>' );
		ruleFields.attr('onchange','SetFieldProperty(\'geocoding_mapping_'+ i +'\',this.value);');

		if ( field['geocoding_mapping_' + i ] !== undefined ) {
			ruleFields.find('option[value="'+ field['geocoding_mapping_' + i ] +'"]').attr('selected','selected');
		}

		html += '<td>' + ruleFields.prop('outerHTML') + '</td>';
		html += '</tr>';
	}

	jQuery( '.geocoding_setting tbody').html( html );


	/*********************************************/

	jQuery('#geocoder_appearance_map').on('change',function(e){
		if ( e.target.checked ) {
			var field = jQuery(e.target).closest('.field_selected');
			field.find('.mapdisplay').removeClass('hidden')
			var field_id = field.attr('id').replace('field_','')
			window['geocode_map_input_' + field_id ].map.invalidateSize();
		} else {
			jQuery(e.target).closest('.field_selected').find('.mapdisplay').addClass('hidden')
		}
	});
	jQuery('#geocoder_appearance_geojson').on('change',function(e){
		if ( e.target.checked ) {
			jQuery(e.target).closest('.field_selected').find('.geojsondisplay').removeClass('hidden')
		} else {
			jQuery(e.target).closest('.field_selected').find('.geojsondisplay').addClass('hidden')
		}
	});
	jQuery('#geocoder_appearance_latlng').on('change',function(e){
		if ( e.target.checked ) {
			jQuery(e.target).closest('.field_selected').find('.latlngdisplay').removeClass('hidden')
		} else {
			jQuery(e.target).closest('.field_selected').find('.latlngdisplay').addClass('hidden')
		}
	});

});

