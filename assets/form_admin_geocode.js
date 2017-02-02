/**
 * On the admin page, each time a geocoder field is edited, set up the mapping for it.
 */
jQuery(document).bind('gform_load_field_settings', function(e, field, form){

	if ( 'geocoder' !== field.type ) {
		return;
	}

	var fields = gfg_geocoders[ form.which_geocoder ];

	var ruleFields;
	var html = '';
	for( var i in fields ) {

		html += '<tr class="default_input_value_row" id="geocoding_source_wrap_' + i + '">';
		html += '<td><label for="geocoding_source_' + i + '" class="inline">' + fields[i] + '</label></td>';

		// Get the rule fields, then unset the onchange attribute
		ruleFields = jQuery( GetRuleFields('geocoding', i, '' ) );
		ruleFields.prepend( '<option value="">Don\'t geocode with this field</option>' );
		ruleFields.attr('onchange','SetFieldProperty(\'geocoding_mapping_'+ i +'\',this.value);');

		if ( field['geocoding_mapping_' + i ] !== undefined ) {
			ruleFields.find('option[value="'+ field['geocoding_mapping_' + i ] +'"]').attr('selected','selected');
		}

		html += '<td>' + ruleFields.prop('outerHTML') + '</td>';
		html += '</tr>';
	}

	jQuery( '.geocoding_setting tbody').html( html );
});
