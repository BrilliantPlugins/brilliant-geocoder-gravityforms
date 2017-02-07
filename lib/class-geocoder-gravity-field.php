<?php
/**
 * This is the geocoder field for gravityforms
 *
 * @package brilliant-geocoder-gravityforms
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * This class provides a geocoder field for GravityForms. It is extensible so that it can support
 * various geocoding services. It defaults to OSM Nomination out of the box, but comes with
 * Google Maps API, Geocod.io and can easily be extended to support other services.
 */
class GF_Field_Geocoder extends GF_Field {

	/**
	 * What kind of field is this?
	 *
	 * @var $type
	 */
	public $type = 'geocoder';

	/**
	 * We only want to print our field once.
	 *
	 * @var $fields_already_printed
	 */
	static $fields_already_printed = array(
		'standard' => array(),
		'appearance' => array(),
		'advanced' => array(),
	);

	/**
	 * Start this up!
	 *
	 * @param array $data Not sure, we just pass it up to the parent field.
	 */
	public function __construct( $data = array() ) {
		parent::__construct( $data );
		if ( ! empty( $data ) ) {
			add_action( 'gform_field_standard_settings', array( $this, 'gform_field_standard_settings' ), 10, 2 );
			add_action( 'gform_field_appearance_settings', array( $this, 'gform_field_appearance_settings' ), 10, 2 );
			add_action( 'gform_field_advanced_settings', array( $this, 'gform_field_advanced_settings' ), 10, 2 );
			add_filter( 'gform_merge_tag_filter', array( $this, 'gform_merge_tag_filter' ), 10, 5 );
			add_filter( 'gform_entries_field_value', array( $this, 'gform_entries_field_value' ), 10, 3 );
		}
	}

	/**
	 * Get the title for the field type.
	 */
	public function get_form_editor_field_title() {
		return esc_attr__( 'Geocoder', 'gravityforms' );
	}

	/**
	 * Get the placement and label for the field button.
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'advanced_fields',
			'text' => esc_html__( 'Geocoder', 'gravityforms' ),
		);
	}

	/**
	 * Get the list of supported settings for this field type.
	 */
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
			'default_value_setting',
			'css_class_setting',
			'geocoding_setting',
			'visibility_setting',
			'description_setting',
		);
	}

	/**
	 * Does what it says on the label.
	 */
	public function is_conditional_logic_supported() {
		return true;
	}

	/**
	 * Get the field html.
	 *
	 * @param object $form The current form.
	 * @param string $value The current value of the field.
	 * @param array  $entry The current entry.
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {
		$form_id         = absint( $form['id'] );
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		$logic_event = ! $is_form_editor && ! $is_entry_detail ? $this->get_conditional_logic_event( 'keyup' ) : '';
		$id          = (int) $this->id;
		$field_id    = $is_entry_detail || $is_form_editor || 0 === $form_id ? "input_$id" : 'input_' . $form_id . "_$id";

		$value        = esc_attr( $value );
		$size         = $this->size;
		$class_suffix = $is_entry_detail ? '_admin' : '';
		$class        = $size . $class_suffix;

		$max_length = is_numeric( $this->maxLength ) ? "maxlength='{$this->maxLength}'" : ''; // @codingStandardsIgnoreLine

		$tabindex              = $this->get_tabindex();
		$disabled_text         = $is_form_editor ? 'disabled="disabled"' : '';
		$required_attribute    = $this->isRequired ? 'aria-required="true"' : ''; // @codingStandardsIgnoreLine
		$invalid_attribute     = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';

		$show_map = ( ! isset( $this->geocoder_appearance_map ) ? true : $this->geocoder_appearance_map );
		$show_geojson = ( ! isset( $this->geocoder_appearance_geojson ) ? false : $this->geocoder_appearance_geojson );
		$show_latlng = ( ! isset( $this->geocoder_appearance_latlng ) ? false : $this->geocoder_appearance_latlng );

		$show_something = ($show_map || $show_geojson || $show_latlng );

		$input = '';

		$geojson = json_decode( html_entity_decode( $value ), true );

		if ( $show_something ) {
			$classes = array();
			if ( $show_map ) {
				$classes[] = 'has_map';
			}
			if ( $show_geojson ) {
				$classes[] = 'has_geojson';
			}
			if ( $show_latlng ) {
				$classes[] = 'has_latlng';
			}
			$input .= '<div class="ginput_complex ginput_container ' . implode( ' ', $classes ) . '">';
		}

		/**
		 * Display the map, with a Leaflet.draw editor
		 */
		if ( $show_map || $is_form_editor || $is_entry_detail ) {
			$leaflet = new LeafletPHP( array(), "geocode_map_$field_id" );

			if ( !$is_form_editor ) {
				$leaflet->add_layer( 'L.geoJSON', array( $geojson ), 'editthis' );
				$leaflet->add_control('L.Control.Draw',array(
					'draw' => array(
						'polyline' => false,
						'polygon' => false,
						'circle' => false,
						'rectangle' => false,
					),
					'edit' => array(
						'featureGroup' => '@@@editthis@@@',
					),
				),'drawControl');
			}

			if ( $is_entry_detail ) {
				$leaflet->add_script( $this->get_form_inline_script_on_page_render( $form, false ) );
			}

			if ( $is_form_editor ) {

				$fe_class = '';
				if ( ! $show_map ) {
					$fe_class = 'hidden';
				}

				$input .= '<div class="mapdisplay ' . $fe_class . '">';
			}

			$input .= '<p>' . $leaflet->get_html() . '</p>';

			if ( $is_form_editor ) {
				$input .= '</div>';
			}
		}

		/**
		 * Show the GeoJSON text input box
		 */
		if ( $show_geojson || $is_form_editor || $is_entry_detail ) {

			if ( $is_form_editor ) {

				$fe_class = '';
				if ( ! $show_geojson ) {
					$fe_class = 'hidden';
				}

				$input .= '<div class="geojsondisplay ' . $fe_class . '">';
			}

			$input .= "<span class='ginput_full'><textarea name='input_{$id}' id='{$field_id}' class='geocoderesults {$class}' {$tabindex} {$logic_event} {$required_attribute} {$invalid_attribute} {$disabled_text}>{$value}</textarea><label for='{$field_id}'>" . esc_html__( 'Location GeoJSON' ) . '</label></span>';

			if ( $is_form_editor ) {
				$input .= '</div>';
			}
		} else {
			$input .= "<input name='input_{$id}' id='{$field_id}' type='hidden' value='{$value}' class='{$class}' {$max_length} {$tabindex} {$logic_event} {$invalid_attribute} {$disabled_text}/>";
		}

		if ( $show_latlng || $is_form_editor || $is_entry_detail ) {

			if ( $is_form_editor ) {

				$fe_class = '';
				if ( ! $show_latlng ) {
					$fe_class = 'hidden';
				}

				$input .= '<div class="latlngdisplay ' . $fe_class . '">';
			}

			if ( is_array( $geojson['geometry'] ) && is_array( $geojson['geometry']['coordinates'] ) ) {
				$lat = $geojson['geometry']['coordinates'][1];
				$lng = $geojson['geometry']['coordinates'][0];
			} else {
				$lat = '';
				$lng = '';
			}
			$input .= '<span class="ginput_left">';
			$input .= "<input class='gf_left_half' id='{$field_id}_lat' type='text' value='{$lat}'><label for='{$field_id}_lat'>" . esc_html__( 'Latitude', 'cimburacom' ) . '</label>';
			$input .= '</span>';
			$input .= '<span class="ginput_right ginput_container">';
			$input .= "<input class='gf_right_half' id='{$field_id}_lng' type='text' value='{$lng}'><label for='{$field_id}_lng'>" . esc_html__( 'Longitude', 'cimburacom' ) . '</label>';
			$input .= '</span>';

			if ( $is_form_editor ) {
				$input .= '</div>';
			}
		}

		$input .= "\n" . '<script>jQuery(document).ready(function(){new gfg_sync_data("' . $field_id . '");});</script>';

		if ( $show_something ) {
			$input .= '</div>';
		}

		return sprintf( "<div class='ginput_container ginput_container_geocoder'>%s</div>", $input );
	}

	/**
	 * Is the submitted field valid?
	 *
	 * @param string $value The value.
	 * @param object $form The current form.
	 */
	public function validate( $value, $form ) {
		return WP_GeoUtil::is_geojson( $value );
	}

	/**
	 * Get the standard settings.
	 *
	 * @param int $position Where should it appear on the page.
	 * @param int $form_id Which form is it for.
	 */
	public function gform_field_standard_settings( $position, $form_id ) {

		if ( 50 === $position ) {
			if ( in_array( 50, GF_Field_Geocoder::$fields_already_printed['standard'], true ) ) {
				return ;
			}

			$form = GFAPI::get_form( $form_id );

			print '<li class="geocoding_setting field_setting">';
			print '<label class="section_label" for="field_admin_label">Geocoding Source Fields</label>';
			print '<p>Configure the mapping for the <em>' . esc_html( $form['which_geocoder'] ) . '</em> eocoding service.</p>';
			print '<table class="default_input_values" id="">';

			print '<thead><tr>';
			print '<td><strong>Field</strong></td>';
			print '<td><strong>Source Field</strong></td>';
			print '</tr></thead><tbody>';

			print '</tbody></table>';
			print '</li>';

			GF_Field_Geocoder::$fields_already_printed['standard'][] = 50;
		}
	}

	/**
	 * Get the appearance settings.
	 *
	 * @param int $position Where should it appear on the page.
	 * @param int $form_id Which form is it for.
	 */
	public function gform_field_appearance_settings( $position, $form_id ) {

		if ( 150 === $position ) {

			if ( in_array( 150, GF_Field_Geocoder::$fields_already_printed['appearance'], true ) ) {
				return ;
			}

			$form = GFAPI::get_form( $form_id );

			$this;

			print '<li class="geocoding_setting field_setting">';
			print '<label class="section_label">Display Type</label>';
			print '<p>' . esc_html__( 'Uncheck all to make this field hidden.' ) . '</p>';

			print '<label for="geocoder_appearance_map"><input id="geocoder_appearance_map" type="checkbox" onchange="SetFieldProperty(\'geocoder_appearance_map\',this.checked);" name="geocoder_appearance_map" value="map"> ' . esc_html__( 'Map' ) . '</label>';
			print '<label for="geocoder_appearance_geojson"><input id="geocoder_appearance_geojson" type="checkbox" onchange="SetFieldProperty(\'geocoder_appearance_geojson\',this.checked);" name="geocoder_appearance_geojson" value="geojson"> ' . esc_html__( 'GeoJSON Textarea' ) . '</label>';
			print '<label for="geocoder_appearance_latlng"><input id="geocoder_appearance_latlng" type="checkbox" onchange="SetFieldProperty(\'geocoder_appearance_latlng\',this.checked);" name="geocoder_appearance_latlng" value="latlng"> ' . esc_html__( 'Latitude and Longitude Fields' ) . '</label>';
			print '</li>';

			GF_Field_Geocoder::$fields_already_printed['appearance'][] = 150;
		}
	}

	/**
	 * Get the advanced settings.
	 *
	 * @param int $position Where should it appear on the page.
	 * @param int $form_id Which form is it for.
	 */
	public function gform_field_advanced_settings( $position, $form_id ) {

		if ( 150 === $position ) {

			if ( in_array( 150, GF_Field_Geocoder::$fields_already_printed['advanced'],true ) ) {
				return ;
			}

			print '<p class="geocoding_setting field_setting">';
			print 'The default value, if set, should be a valid GeoJSON string. Probably a point.';
			print '</p>';

			GF_Field_Geocoder::$fields_already_printed['advanced'][] = 150;
		}
	}

	/**
	 * Get the custom non-static JS that we need to do the geocoding. Probably mostly API keys and such.
	 *
	 * @param object $form The current form object.
	 * @param bool   $include_form_id_bit Should we include the form ID in the input_ id. False for admin pages.
	 */
	public function get_form_inline_script_on_page_render( $form, $include_form_id_bit = true ) {
		$geocoders = $this->get_geocoder_field_mapping();
		$gfg = Geocoder_for_Gravity::get_instance();
		$geocoding_engine = $gfg->get_engine_for_geocoder( $form['which_geocoder'] );

		$fields = $geocoders[ $form['which_geocoder'] ];

		$form_id_bit = $this->formId . '_'; // @codingStandardsIgnoreLine

		if ( ! $include_form_id_bit ) {
			$form_id_bit = '';
		}

		$my_selector = 'input_' . $form_id_bit . $this->id;
		$selectors = array();
		foreach ( $fields as $field => $label ) {
			$key = 'geocoding_mapping_' . $field;
			if ( ! empty( $this->$key ) ) {
				$selector = 'input_' . $form_id_bit . str_replace( '.','_',$this->$key );
				$selectors[ $selector ] = $field;
			}
		}

		$script = "\n" . 'gfg_geocodings.' . $my_selector . ' = ' . wp_json_encode( array( 'fields' => $selectors, 'engine' => $geocoding_engine ) ) . ';';
		$script .= "\n" . 'jQuery("#' . implode( ',#', array_keys( $selectors ) ) . '").on("change", gfg_update_geocoder);' . "\n";

		$extra_keys = array();
		if ( 'nomination' === $geocoding_engine ) {
			$extra_keys['email'] = get_bloginfo( 'admin_email' );
			$extra_keys['format'] = 'jsonv2';
			$extra_keys['extratags'] = 1;
			$extra_keys['limit'] = 1;
		}

		$extra_keys = apply_filters( 'gfg_geocoder_keys', $extra_keys, $geocoding_engine, $form );

		if ( ! empty( $extra_keys ) ) {
			$script .= "\n" . 'gfg_geocoder_keys.' . $geocoding_engine . ' = ' . wp_json_encode( $extra_keys ) . ';';
		}

		return $script;
	}

	/**
	 * Get a list of geocoders.
	 */
	public function get_form_editor_inline_script_on_page_render() {
		$some_js = parent::get_form_editor_inline_script_on_page_render();

		$geocoders = $this->get_geocoder_field_mapping();
		$some_js .= 'window.gfg_geocoders = ' . wp_json_encode( $geocoders ) . ';';
		$some_js .= "\njQuery(document).bind('gform_load_field_settings', function(event,field,form){
			jQuery('#geocoder_appearance_map').prop('checked',(field.geocoder_appearance_map === undefined ? true : field.geocoder_appearance_map));
			jQuery('#geocoder_appearance_geojson').prop('checked',(field.geocoder_appearance_geojson === undefined ? false : field.geocoder_appearance_geojson));
			jQuery('#geocoder_appearance_latlng').prop('checked',(field.geocoder_appearance_latlng === undefined ? false : field.geocoder_appearance_latlng));
	});";
return $some_js;
	}

	/**
	 * Get the geocoder labels and field names.
	 */
	public function get_geocoder_field_mapping() {

		$geocoders = array(
			'OSM Nomination simple query' => array(
				'q'				=> 'Search Field',
			),
			'OSM Nomination full address' => array(
				'street'		=> 'Street',
				'city'			=> 'City',
				'county'		=> 'County',
				'state'			=> 'State',
				'country'		=> 'Country',
				'postalcode'	=> 'Postal Code',
				'countrycode'	=> 'ISO 3166-1alpha2 Country Code',
			),
		);

		$geocoders = apply_filters( 'gfg_geocoders_fields', $geocoders );

		return $geocoders;
	}

	/**
	 * Un-escape whatever GF does to most text fields.
	 *
	 * @param string $value The modified value.
	 * @param string $merge_tag The merge tag it thinks we're merging.
	 * @param string $modifier Don't know.
	 * @param object $field The current field.
	 * @param string $raw_value The original raw value.
	 *
	 * If the $merge_tag is a string equal to the ID of the current field, then
	 * it's the original save event and we should return the raw value. Otherwise,
	 * we actually are in a merge_tag, and we should return the $value, which will
	 * already have gone through get_value_entry_detail and gotten cleaned up.
	 */
	public function gform_merge_tag_filter( $value, $merge_tag, $modifier, $field, $raw_value ) {
		if ( 'geocoder' === $field->type ) {
			if ( (string) $field->id === $merge_tag ) {
				return $raw_value;
			} else {
				return $value;
			}
		}

		return $value;
	}

	/**
	 * This function modifies a value before its used in emails, etc.
	 *
	 * We're going to print a web map in certain circumstances, but flat coords for
	 * emails and print.
	 *
	 * @param string $value The submitted value.
	 * @param string $currency Waht kind of currency.
	 * @param bool   $use_text Should this use text.
	 * @param string $format Whats the expected output format.
	 * @param string $media What's the output media.
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		if ( 'screen' === $media && 'html' === $format && false === $use_text ) {
			$leaflet = new LeafletPHP();
			$leaflet->add_layer( 'L.geoJSON', array( json_decode( $value, true ) ) );
			$html = $leaflet->get_html();
			$html .= '<div><textarea class="geocoderesults">' . $value . '</textarea></div>';
			return $html;
		}

		return $this->make_human_readable_cords( $value );
	}

	/**
	 * When displayed with other entries, we just want the abbreviated version.
	 *
	 * @param string $value The value that is to be displayed.
	 * @param int    $form_id The form id.
	 * @param int    $field_id The field id.
	 */
	public function gform_entries_field_value( $value, $form_id, $field_id ) {
		$form         = GFAPI::get_form( $form_id );
		$field        = RGFormsModel::get_field( $form, $field_id );

		if ( 'geocoder' === $field->type ) {
			return $this->make_human_readable_cords( html_entity_decode( $value ) );
		}
		return $value;
	}

	/**
	 * Format GeoJSON for human consumption. Since this is geocoding, we're going to assume
	 * we're dealing with a point coordinate.
	 *
	 * @param string $value A value, probably GeoJSON.
	 *
	 * Returns the value as-is if it's not parsable as a point.
	 */
	public function make_human_readable_cords( $value ) {
		if ( WP_GeoUtil::is_geojson( $value, true ) ) {
			$json = json_decode( $value, true );
			$geom = $json['geometry'];
			if ( 'Point' !== $geom['type'] ) {
				return $value;
			}

			return implode( ', ', array_reverse( $geom['coordinates'] ) );

		} else {
			return $value;
		}
	}
}

// Let's do this!
GF_Fields::register( new GF_Field_Geocoder() );
