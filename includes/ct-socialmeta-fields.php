<?php
/**
 * Add carbon fields as dependencies for this plugin.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ct_Socialmeta
 * @subpackage Ct_Socialmeta/includes
 * @author     ChildThemes <hello@childthemes.net>
 */
class CT_Socialmeta_Fields {

    /**
     * @var $path
     */
    protected $path = CT_SOCIALMETA_PATH . 'includes/options/';

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct() {

        add_action( 'carbon_register_fields', array( $this, 'includes' ) );

    }

    /**
     * Include all plugin option files
     *
     * @since    1.0.0
     */
    public function includes() {

        /**
         * Include default settings for meta.
         */
        include_once( $this->path . 'plugin-options.php' );

        /**
         * Include individual post meta settings
         */
        include_once( $this->path . 'post-fields.php' );

    }

}
