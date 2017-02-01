<?php

class Geocoder_for_Gravity extends GFAddOn {

	protected $_version = GFG_VERSION;
	protected $_min_gravityforms_version = '2.1.2';
	protected $_slug = 'geocoderforgf';
	protected $_path = 'idk';
	protected $_full_path = __FILE__;
	protected $_title = 'Geocoder for GravityForms';
	protected $_short_title = 'Geocoder';
	protected $_capabilities_settings_page = 'idkeither';

	private static $_instance = null;

	/**
	 * Returns an instance of this class, and stores it in the $_instance property.
	 *
	 * @return object $_instance An instance of this class.
	 **/
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function init(){
		parent::init();
		add_action('gform_field_standard_settings', array($this, 'add_field_settings'), 10, 2);
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'enqueue_scripts', array( $this, 'enqueue_scripts' ), 10, 2 );
		add_filter( 'gform_form_settings', array( $this, 'gform_form_settings' ), 10, 2 );
		add_filter( 'gform_pre_form_settings_save', array( $this, 'gform_pre_form_settings_save' ) );
	}

	public function add_field_settings( $position, $form_id ) {

		if ( 0 === $position ) {
			$html = '<li class="field setting">Geocoder Settings</li>';
			print $html;
		}
	}

	public function get_geocoders() {
		$geocoders = array(
			array(
				'name' => 'geocodio_key',
				'label' => 'Geocod.io API Key',
				'type' => 'text',
				'class' => 'small',
				'placeholder' => 'Geocod.io API Key',
				'geocoder' => 'Geocod.io simple query',
				'geocoder_engine' => 'geocodio',
			),
			array(
				'name' => 'geocodio_key',
				'geocoder' => 'Geocod.io full address',
				'geocoder_engine' => 'geocodio',
			),
			array(
				'name' => 'google_maps_key',
				'label' => 'Google Maps API Key', 
				'type' => 'text',
				'class' => 'small',
				'placeholder' => 'Google Maps API Key',
				'geocoder' => 'Google Maps API',
				'geocoder_engine' => 'google_maps',
			),
			array(
				'geocoder' => 'OSM Nomination simple query',
				'geocoder_engine' => 'nomination',
			),
			array(
				'geocoder' => 'OSM Nomination full address',
				'geocoder_engine' => 'nomination',
			)
		);

		$geocoders = apply_filters( 'gfg_geocoders', $geocoders );

		return $geocoders;
	}

	public function get_engine_for_geocoder( $in_use_geocoder ) {
		$geocoders = $this->get_geocoders();
		foreach( $geocoders as $geocoder ) {
			if ( $geocoder[ 'geocoder' ] === $in_use_geocoder ) {
				return $geocoder[ 'geocoder_engine' ];
			}
		}

		return false;
	}

	public function plugin_settings_fields() {
		$settings = $this->get_plugin_settings();

		$geocoders = $this->get_geocoders();

		$geocoders = array_filter( $geocoders, function( $g ){
			return array_key_exists( 'label', $g );
		});

		$geocoders[] = array(
			'id'    => 'save_button',
			'type'  => 'save',
			'value' => 'Update',
		);

		$form_def = array(
			array(
				'description' => $this->plugin_settings_description(),
				'fields' => $geocoders
			)
		);

		return $form_def;
	}

	/**
	 * Prepare custom app settings settings description.
	 *
	 * @access public
	 * @return string $description
	 */
	public function plugin_settings_description() {
		/* Introduction. */
		$description = '';
		$description .= '<h2>Geocoding settings</h2>';
		$description .= '<p>http://wiki.openstreetmap.org/wiki/Nominatim, https://geocod.io/docs/#single-address</p>';

		return $description;
	}

	public function admin_enqueue_scripts() {
		if ( GFForms::get_page() === 'form_editor' ) {
			$base_url = plugins_url( '', dirname( __FILE__ ) );
			wp_enqueue_script( 'form_admin_geocode', $base_url . '/assets/form_admin_geocode.js', array( 'jquery' ), $this->_version );
			wp_enqueue_style( 'form_admin_geocode', $base_url . '/assets/form_admin_geocode.css', array( ), $this->_version );
		}
	}

	public function enqueue_scripts( $form = '', $is_ajax = false ) {
		parent::enqueue_scripts( $form, $is_ajax );
		$base_url = plugins_url( '', dirname( __FILE__ ) );
		wp_enqueue_script( 'gfg_geocode', $base_url . '/assets/form_geocode.js', array( 'jquery' ), $this->_version );
	}

	public function gform_form_settings( $settings, $form ) {

		// Get plugin settings so we can see if we have needed API keys 
		$plugin_settings = $this->get_plugin_settings();

		// See which geocoder we're using. Default to the OSM Nomination geocoder
		$selected_geocoder = rgar( $form, 'which_geocoder' );
		$selected_geocoder = ( empty( $selected_geocoder ) ? 'OSM Nomination simple query' : $selected_geocoder );

		// Build up the options
		$options = '';
		$geocoders = $this->get_geocoders();
		foreach( $geocoders as $geocoder ) {

			// Check if we have the required keys for the service
			if ( array_key_exists( 'label', $geocoder ) && empty( $plugin_settings[ $geocoder['name'] ] ) ) {
				continue;
			}

			$selected = '';
			if ( $selected_geocoder === $geocoder['geocoder'] ) {
				$selected = ' selected="selected"';
			} 

			$options[$geocoder['geocoder']] = '<option' . $selected . ' value="' . esc_attr( $geocoder['geocoder'] ) . '">' . esc_html( $geocoder['geocoder'] ) . '</option>';
		}

		ksort( $options );

		// Make the settings string
		$setting = '<tr><th><label for="which_geocoder">Geocoder engine</label></th><td><select name="which_geocoder">';
		$setting .= implode('',$options);
		$setting .= '</select></td></tr>';

		$settings['Geocoder']['which_geocoder'] = $setting;

		return $settings;
	}

	public function gform_pre_form_settings_save( $form ) {
		$form['which_geocoder'] = rgpost( 'which_geocoder' );
		return $form;
	}
}
