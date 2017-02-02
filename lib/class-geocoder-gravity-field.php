<?php

// https://www.gravityhelp.com/documentation/article/gf_field/

if(!class_exists('GFForms')){
	die();
}

class GF_Field_Geocoder extends GF_Field {

	public $type = 'geocoder';

    /**
     * We only want to print our field once.
     *
     * @var $fields_already_printed
     */
    static $fields_already_printed = array();

	public function __construct( $data = array() ){
		parent::__construct( $data );
		if ( !empty( $data ) ) {
			add_action( 'gform_field_advanced_settings', array( $this, 'gform_field_advanced_settings' ), 10, 2 );
		}
	}

	public function get_form_editor_field_title() {
		return esc_attr__( 'Geocoder', 'gravityforms' );
	}

	public function get_form_editor_button() {
		return array(
			'group' => 'advanced_fields',
			'text' => 'Geocoder'
		);
	}

	public function get_form_editor_field_settings() {
		return array(
			'conditional_logic_field_setting',
			'prepopulate_field_setting',
			'error_message_setting',
			'label_setting',
			'label_placement_setting',
			'admin_label_setting',
			'size_setting',
			'rules_setting',
			'visibility_setting',
			'default_value_setting',
			'css_class_setting',
			'geocoding_setting',
		);	
	}

	public function is_conditional_logic_supported() {
		return true;
	}

    public function get_field_input( $form, $value = '', $entry = null ) {
        $form_id         = absint( $form['id'] );
        $is_entry_detail = $this->is_entry_detail();
        $is_form_editor  = $this->is_form_editor();

        $logic_event = ! $is_form_editor && ! $is_entry_detail ? $this->get_conditional_logic_event( 'keyup' ) : '';
        $id          = (int) $this->id;
        $field_id    = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

        $value        = esc_attr( $value );
        $size         = $this->size;
        $class_suffix = $is_entry_detail ? '_admin' : '';
        $class        = $size . $class_suffix;

        $max_length = is_numeric( $this->maxLength ) ? "maxlength='{$this->maxLength}'" : '';

        $tabindex              = $this->get_tabindex();
        $disabled_text         = $is_form_editor ? 'disabled="disabled"' : '';
        $required_attribute    = $this->isRequired ? 'aria-required="true"' : '';
        $invalid_attribute     = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';

        $input = "<input name='input_{$id}' id='{$field_id}' type='text' value='{$value}' class='{$class}' {$max_length} {$tabindex} {$logic_event} {$invalid_attribute} {$disabled_text}/>";

        return sprintf( "<div class='ginput_container ginput_container_geocoder'>%s</div>", $input );
    }

	public function validate( $value, $form ) {
		return WP_GeoUtil::is_geojson( $value );	
	}

	public function gform_field_advanced_settings( $position, $form_id ) {

		if ( $position === 50 ) {
			if ( in_array( '50', GF_Field_Geocoder::$fields_already_printed ) ) {
				return ;
			}

			$form = GFAPI::get_form( $form_id );

			print '<li class="geocoding_setting field_setting">';
			print '<label for="field_admin_label">Geocoding Source Fields</label>';
			print '<p>Configure the mapping for the <em>' . $form['which_geocoder'] . '</em> eocoding service.</p>';
			print '<table class="default_input_values" id="">';

			print '<thead><tr>';
				print '<td><strong>Field</strong></td>';
				print '<td><strong>Source Field</strong></td>';
			print '</tr></thead><tbody>';

			print '</tbody></table>';
			print '</li>';

			GF_Field_Geocoder::$fields_already_printed[] = 50;

		} else if ( $position === 150 ) {

			if ( in_array( '150', GF_Field_Geocoder::$fields_already_printed ) ) {
				return ;
			}

			print '<p class="geocoding_setting field_setting">';
			print 'The default value, if set, should be a valid GeoJSON string. Probably a point.';
			print '</p>';

			GF_Field_Geocoder::$fields_already_printed[] = 150;
		}
	}

	public function get_form_inline_script_on_page_render( $form ) {
		$geocoders = $this->get_geocoder_field_mapping();
		$gfg = Geocoder_for_Gravity::get_instance();
		$geocoding_engine = $gfg->get_engine_for_geocoder( $form[ 'which_geocoder' ] );

		$fields = $geocoders[ $form['which_geocoder' ] ];

		$my_selector = 'input_' . $this->formId . '_' . $this->id;
		$selectors = array();
		foreach( $fields as $field => $label ) {
			$key = 'geocoding_mapping_' . $field;
			if ( !empty( $this->$key ) ) {
				$selector = 'input_' . $this->formId . '_' . str_replace('.','_',$this->$key);
				$selectors[ $selector ] = $field;
			}
		}

		$script = "\n" . 'gfg_geocodings.' . $my_selector . ' = ' . json_encode( array( 'fields' => $selectors, 'engine' => $geocoding_engine ) ) . ';';
		$script .= "\n" . 'jQuery("#' . implode(',#', array_keys( $selectors ) ) . '").on("change", gfg_update_geocoder);' . "\n";

		$extra_keys = array();
		if ( 'nomination' === $geocoding_engine ) {
			$extra_keys['email'] = get_bloginfo('admin_email');
			$extra_keys['format'] = 'jsonv2';
			$extra_keys['extratags'] = 1;
			$extra_keys['limit'] = 1;
		}

		$extra_keys = apply_filters( 'gfg_geocoder_keys', $extra_keys, $geocoding_engine, $form );

		if ( !empty( $extra_keys ) ) {
			$script .= "\n" . 'gfg_geocoder_keys.' . $geocoding_engine . ' = ' . json_encode( $extra_keys ) . ';';
		}

		return $script;
	}

	public function get_form_editor_inline_script_on_page_render() {
		$someJS = parent::get_form_editor_inline_script_on_page_render();


		$geocoders = $this->get_geocoder_field_mapping();
		$someJS .= 'window.gfg_geocoders = ' . json_encode( $geocoders ) . ';';
		return $someJS;
	}

	public function get_geocoder_field_mapping() {

		$geocoders = array(
			'OSM Nomination simple query' => array(
				'q'				=> 'Search Field'
			),
			'OSM Nomination full address' => array(
				'street'		=> 'Street',
				'city'			=> 'City',
				'county'		=> 'County',
				'state'			=> 'State',
				'country'		=> 'Country',
				'postalcode'	=> 'Postal Code',
				'countrycode'	=> 'ISO 3166-1alpha2 Country Code'
			)
		);

		$geocoders = apply_filters( 'gfg_geocoders_fields', $geocoders );

		return $geocoders;
	}

	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {
		/*
		 * This is a dumb hack.
		 * gravityforms/common.php:4801 escapes square brackets for all types except html, section and signature.
		 * So...we pretend we're just an html field.
		 *
		 * This will never come back to bite us, right?
		 */
		$this->type = 'html';
		return $value;
	}
}

GF_Fields::register( new GF_Field_Geocoder() );
