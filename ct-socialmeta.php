<?php
/**
 * Child Social Meta Plugin File
 *
 * @link              http://childthemes.net/
 * @since             1.0.0
 * @package           Ct_Socialmeta
 *
 * @wordpress-plugin
 * Plugin Name:       Child Social Meta
 * Plugin URI:        http://childthemes.net/
 * Description:       Fast, simple no annoying ads or generator social meta to enchant your sharing content on Facebook, Twitter, Google+
 * Version:           1.0.0
 * Author:            ChildThemes
 * Author URI:        http://childthemes.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ct-socialmeta
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'CT_SOCIALMETA_VER', '1.0.0' );
define( 'CT_SOCIALMETA_BASE', plugin_basename( __FILE__ ) );
define( 'CT_SOCIALMETA_DIR', untrailingslashit( dirname( CT_SOCIALMETA_BASE ) ) );
define( 'CT_SOCIALMETA_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );

/**
 * Helpel function This Plugin
 * Use this can be as static function and callable.
 */
require CT_SOCIALMETA_PATH . 'includes/ct-socialmeta-functions.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require CT_SOCIALMETA_PATH . 'includes/ct-socialmeta-core.php';

/**
 * Disable and clear all cached transient
 * From database when plugin is disabled or deactivated.
 */
register_deactivation_hook( __FILE__, 'ctsm_purge_cache' );

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
$GLOBALS['ct_socialmeta'] = new CT_Socialmeta();
