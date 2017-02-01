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
    static $fields_already_printed = false;

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

            if ( GF_Field_Geocoder::$fields_already_printed ) {
                return;
            }

            GF_Field_Geocoder::$fields_already_printed = true;

			print '<li class="geocoding_setting field_setting">';
				print '<label for="field_admin_label">Geocoding Source Fields</label>';
				print '<table class="default_input_values"><tbody><tr><td><strong>Field</strong></td><td><strong>Default Value</strong></td></tr><tr class="default_input_value_row" data-input_id="2.1" id="input_default_value_row_input_2_1"><td><label for="field_default_value_2.1" class="inline">Street Address</label></td><td><input class="default_input_value" value="" id="field_default_value_2.1" type="text"></td></tr><tr class="default_input_value_row" data-input_id="2.2" id="input_default_value_row_input_2_2"><td><label for="field_default_value_2.2" class="inline">Address Line 2</label></td><td><input class="default_input_value" value="" id="field_default_value_2.2" type="text"></td></tr><tr class="default_input_value_row" data-input_id="2.3" id="input_default_value_row_input_2_3"><td><label for="field_default_value_2.3" class="inline">City</label></td><td><input class="default_input_value" value="" id="field_default_value_2.3" type="text"></td></tr><tr class="default_input_value_row" data-input_id="2.4" id="input_default_value_row_input_2_4"><td><label for="field_default_value_2.4" class="inline">State / Province</label></td><td><input class="default_input_value" value="" id="field_default_value_2.4" type="text"></td></tr><tr class="default_input_value_row" data-input_id="2.5" id="input_default_value_row_input_2_5"><td><label for="field_default_value_2.5" class="inline">ZIP / Postal Code</label></td><td><input class="default_input_value" value="" id="field_default_value_2.5" type="text"></td></tr><tr class="default_input_value_row" data-input_id="2.6" id="input_default_value_row_input_2_6"><td><label for="field_default_value_2.6" class="inline">Country</label></td><td><input class="default_input_value" value="" id="field_default_value_2.6" type="text"></td></tr></tbody></table>';
			print '</li>';
		}
	}


}

// GetRuleFields

GF_Fields::register( new GF_Field_Geocoder() );
