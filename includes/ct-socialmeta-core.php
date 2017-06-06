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
        add_action( 'wp_ajax_ct_socialmeta_dismiss_warning', array( $this, 'hide_warning' ) );

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
        $option_template = get_transient( 'ct_socialmeta_head_support_hide_' . $template );

        if ( ! $option_template ) {
            add_action( 'admin_notices', array( $this, 'add_template_head_notice' ) );
        }
    }

    /**
	 * Hide warning on button dismiss clicked
	 *
	 * @since     1.0.0
	 * @return    void
	 */
    public function hide_warning() {

        if ( empty( $_POST['template'] ) ) {
            echo '0'; wp_die();
        }

        global $wpdb;

        $template = sanitize_text_field( $_POST['template'] );
        $cache_key = 'ct_socialmeta_head_support_'.$template;
        $cache_hide_key = 'ct_socialmeta_head_support_hide_'.$template;

        check_ajax_referer( 'ct-socialmeta-dismiss-template-warning', 'security', true );
        set_transient( $cache_key, true, false );
        set_transient( $cache_hide_key, true, false );
        wp_die();
    }

    /**
	 * Add admin notice if current template doesn't support head attribute hook.
	 *
	 * @since     1.0.0
	 * @return    void
	 */
	public function add_template_head_notice() {
        $template = get_template();
        $template_path = get_template_directory();
        $file_path = str_replace( ABSPATH, '', $template_path );
        $ajax_nonce = wp_create_nonce( 'ct-socialmeta-dismiss-template-warning' );
        $ask_confirm = esc_attr__('Are you sure to hide this warning? This can not be undone!', 'ct-socialmeta');
        ?>
        <div id="ct-socialmeta-message" class="notice notice-warning ct-socialmeta-notice">
            <p>
                <strong><?php esc_html_e( 'WARNING!!!' ) ?></strong><br />
                <?php printf(
                __( 'For use Opeh Graph meta tags correctly, your current active theme must supported custom HTML head attribute, please add it manually.<br />Replace your <code>&lt;head&gt;</code> with %s at file %s', 'ct-socialmeta' ),
                "<code>&lt;head &lt;?php do_action( &#39;add_head_attributes&#39; ); ?&gt;&gt;</code>",
                "<code>$file_path/header.php</code>"
            ); ?></p>
            <p style="margin-top:10px"><button type="button" id="ctSocialMetaDismiss" class="button button-small">
                <span class="dashicons dashicons-hidden" style="font-size:16px;line-height:inherit;"></span>
                <span><?php esc_attr_e('Hide warning for theme','ct-socialmeta') ?> : <?php echo $template ?></span>
            </button></p>
            <script type="text/javascript">
                jQuery('#ctSocialMetaDismiss').click(function(e) {
                    e.preventDefault();
                    var checkTrue = confirm('<?php echo $ask_confirm ?>');
                    if (checkTrue == true) {
                        jQuery(this).addClass('disabled');
                        var data = {
                            action: 'ct_socialmeta_dismiss_warning',
                            security: '<?php echo $ajax_nonce ?>',
                            template: '<?php echo $template ?>'
                        };
                        jQuery.post(ajaxurl, data, function(response) {
                            jQuery('#ct-socialmeta-message').slideUp('fast', 'linear', function() {
                                jQuery('#ct-socialmeta-message').remove();
                            });
                        });
                    }
                });
            </script>
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
