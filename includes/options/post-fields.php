<?php
/**
 * Post Meta Custom Fields.
 *
 * @link       http://childthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Socialmeta
 * @subpackage CT_Socialmeta/includes
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

$post_types = (array) carbon_get_theme_option('ctsm_metabox_post_type');
