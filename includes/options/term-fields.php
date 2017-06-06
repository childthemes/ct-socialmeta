<?php
/**
 * Term Meta Custom Fields.
 *
 * @link       http://childthemes.net/
 * @since      1.0.0
 *
 * @package    CT_Socialmeta
 * @subpackage CT_Socialmeta/includes
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field;

$taxonomies = (array) carbon_get_theme_option('ctsm_metabox_taxonomy');
$fb_type = carbon_get_theme_option('ctsm_facebook_type');
$tw_type = carbon_get_theme_option('ctsm_twitter_style');

Container::make('term_meta', __('Custom Social Meta', 'ct-socialmeta'))
->show_on_taxonomy( $taxonomies )
->add_fields(array(
    Field::make('text', 'ctsm_default_title', __('Social Meta Title', 'ct-socialmeta'))
        ->help_text('<p class="description">'.__('Leave empty to use default title', 'ct-socialmeta').'</p>'),
    Field::make('textarea', 'ctsm_default_desc', __('Social Meta Description', 'ct-socialmeta'))->set_rows(4)
        ->help_text('<p class="description">'.__('Leave empty to use default description', 'ct-socialmeta').'</p>'),
    Field::make('image', 'ctsm_default_image', __('Social Meta Image', 'ct-socialmeta'))
))

;
