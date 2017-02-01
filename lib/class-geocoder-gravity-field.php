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

			print '<li class="geocoding_setting field_setting">';
			print '<label for="field_admin_label">Geocoding Source Fields</label>';
			print '<p>Configure the mapping for the Geocoding service. Not all services require all fields.</p>';
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

			print '<p>';
			print 'The default value, if set, should be a valid GeoJSON string. Probably a point.';
			print '</p>';

			GF_Field_Geocoder::$fields_already_printed[] = 150;
		}
	}

	public function get_form_inline_script_on_page_render( $form ) {
		$script = 'console.log("inline script on page render")';
		return $script;
	}

	public function get_form_editor_inline_script_on_page_render() {
		$script = 'console.log("inline editor script on page render")';
		return $script;
	}
}

// GetRuleFields

GF_Fields::register( new GF_Field_Geocoder() );
