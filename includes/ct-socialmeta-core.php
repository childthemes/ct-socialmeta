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
        add_action( 'load-settings_page_crbn-child-social-meta', array( $this, 'check_warning' ) );
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
	 * Check if current template support head attributes
	 *
	 * @since     1.0.0
	 * @return    void
	 */
    public function check_warning() {

        $template = get_template();
        $option_template = get_option( 'ct_socialmeta_head_support_' . $template );

        if ( ! $option_template ) {
            add_action( 'admin_notices', array( $this, 'add_template_head_notice' ) );
        }
    }

    /**
	 * Add admin notice if current template doesn't support head attribute hook.
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	public function add_template_head_notice() {
        $template_path = get_template_directory();
        $file_path = str_replace( ABSPATH, '', $template_path );
        ?>
        <div id="ct-socialmeta-message" class="notice notice-warning ct-socialmeta-notice">
            <p>
                <strong><?php esc_html_e( 'WARNING!!!' ) ?></strong><br />
                <?php printf(
                __( 'For use Opeh Graph meta tags, your current active theme must supported custom HTML head attribute, please add it manually.<br />Replace your <code>&lt;head&gt;</code> with %s at file %s', 'ct-socialmeta' ),
                "<code>&lt;head &lt;?php do_action( &#39;add_head_attributes&#39; ); ?&gt;&gt;</code>",
                "<code>$file_path/header.php</code>"
            ); ?></p>
        </div>
        <?php
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
