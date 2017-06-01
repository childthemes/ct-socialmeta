<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://childthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Socialmeta
 * @subpackage CT_Socialmeta/includes
 */

class CT_Socialmeta {

	/**
	 * @var CT_Socialmeta_Fields
	 */
	public $fields;

    /**
	 * @var CT_Socialmeta_Generator
	 */
	public $generator;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'ct-socialmeta';
		$this->version = '1.0.0';

        add_action( 'plugins_loaded', array( $this, 'load_dependencies' ) );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function load_dependencies() {

        $this->load_plugin_textdomain();
        $this->add_carbon_fields();

		/**
		 * The class responsible for defining custom fields for all post type.
		 */
		require_once CT_SOCIALMETA_PATH . 'includes/ct-socialmeta-fields.php';
        // Init Fields
        $this->fields = new CT_Socialmeta_Fields();

        /**
		 * The class responsible for generate social meta at the front facing site
		 */
		require_once CT_SOCIALMETA_PATH . 'includes/ct-socialmeta-generator.php';
        // Init Generator
        $this->generator = new CT_Socialmeta_Generator();

	}

    /**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'ct-socialmeta',
			false,
			CT_SOCIALMETA_PATH . 'languages/'
		);
	}

	/**
	 * Check for existence Carbon Fields as Plugin, nor this will include
     * Carbon Fields from our plugin.
	 *
     * @link      https://wordpress.org/plugins/carbon-fields/
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	private function add_carbon_fields() {

        if ( !defined('Carbon_Fields\PLUGIN_FILE') ) {
            require_once CT_SOCIALMETA_PATH . 'includes/carbon-fields/carbon-fields-plugin.php';
        }

	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
