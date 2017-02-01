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
	}

	public function add_field_settings( $position, $form_id ) {

		if ( 0 === $position ) {
			$html = '<li class="field setting">Geocoder Settings</li>';
			print $html;
		}
	}

	public function plugin_settings_fields() {
		$settings = $this->get_plugin_settings();

		$form_def = array(
			array(
				'description' => $this->plugin_settings_description(),
				'fields' => array(
					array(
						'name' => 'geocodio_key',
						'label' => 'Geocod.io API Key',
						'type' => 'text',
						'class' => 'small',
						'placeholder' => 'Geocod.io API Key'
					)
				)
			)
		);

		if(count($fields) > 0){
			$form_def[] = array(
				'fields' => array(
					array(
						'id'    => 'save_button',
						'type'  => 'save',
						'value' => 'Update',
					),
				)
			);
		}

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

		return $description;
	}

	public function admin_enqueue_scripts() {
		if ( GFForms::get_page() === 'form_editor' ) {
			$base_url = plugins_url( '', dirname( __FILE__ ) );
			wp_enqueue_script( 'form_admin_geocode', $base_url . '/assets/form_admin_geocode.js', array( 'jquery' ), $this->_version );
			wp_enqueue_style( 'form_admin_geocode', $base_url . '/assets/form_admin_geocode.css', array( ), $this->_version );
		}
	}
}
