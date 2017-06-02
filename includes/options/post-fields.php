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

Container::make('post_meta', __('Custom Social Meta', 'ct-socialmeta'))
->show_on_post_type( $post_types )

/**
 * GENERAL SETTINGS
 */
->add_tab( __('General', 'ct-socialmeta'), array(
    Field::make('text', 'ctsm_defaut_title', __('Social Meta Title', 'ct-socialmeta'))->set_width(70)
        ->help_text('<small>'.__('Leave empty to use default title', 'ct-socialmeta').'</small>'),
    Field::make('image', 'ctsm_default_image', __('Social Meta Image', 'ct-socialmeta'))->set_width(30),
    Field::make('textarea', 'ctsm_default_desc', __('Social Meta Description', 'ct-socialmeta'))->set_rows(2)
        ->help_text('<small>'.__('Leave empty to use default excerpt or content', 'ct-socialmeta').'</small>')
))
/**
 * FACEBOOK SETTINGS
 */
->add_tab( __('Facebook Settings', 'ct-socialmeta'), array(
    Field::make('checkbox', 'ctsm_facebook_is_default', __('Use Default Facebook Social Meta Settings', 'ct-socialmeta'))
        ->set_option_value('yes')
        ->set_default_value('yes'),

    Field::make('select', 'ctsm_facebook_type', __('Facebook Site Type', 'ct-socialmeta'))->set_options('ctsm_facebook_site_types')
    ->set_conditional_logic(array( array( 'field' => 'ctsm_facebook_is_default' ) )),

    Field::make('text', 'ctsm_facebook_profile', __('Facebook Profile ID', 'ct-socialmeta'))
    ->help_text(sprintf(__('Find your ID at %s', 'ct-socialmeta'), '<a href="https://findmyfbid.com/" target="_blank">FindMyFbId</a>'))
    ->set_conditional_logic(array(
        array( 'field' => 'ctsm_facebook_is_default' ),
        array( 'field' => 'ctsm_facebook_type', 'value' => 'profile' )
    )),

    Field::make('text', 'ctsm_facebook_article_author', __('Author Profile ID', 'ct-socialmeta'))->set_width(50)
    ->set_conditional_logic(array(
        array( 'field' => 'ctsm_facebook_is_default' ),
        array( 'field' => 'ctsm_facebook_type', 'value' => 'article' )
    )),
    Field::make('text', 'ctsm_facebook_article_section', __('Article Section/Category', 'ct-socialmeta'))->set_width(50)
    ->set_conditional_logic(array(
        array( 'field' => 'ctsm_facebook_is_default' ),
        array( 'field' => 'ctsm_facebook_type', 'value' => 'article' )
    )),

    Field::make('separator', 'ctsm_facebook_business_sep', __('Business Address', 'ct-socialmeta'))
    ->set_conditional_logic(array(
        array( 'field' => 'ctsm_facebook_is_default' ),
        array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' )
    )),
    Field::make('text', 'ctsm_facebook_business_street_address', __('Street Address', 'ct-socialmeta'))
    ->set_conditional_logic(array(
        array( 'field' => 'ctsm_facebook_is_default' ),
        array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' )
    )),
    Field::make('text', 'ctsm_facebook_business_locality', __('City', 'ct-socialmeta'))->set_width(50)
    ->set_conditional_logic(array(
        array( 'field' => 'ctsm_facebook_is_default' ),
        array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' )
    )),
    Field::make('text', 'ctsm_facebook_business_region', __('Region', 'ct-socialmeta'))->set_width(50)
    ->set_conditional_logic(array(
        array( 'field' => 'ctsm_facebook_is_default' ),
        array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' )
    )),
    Field::make('text', 'ctsm_facebook_business_postal_code', __('Post Code', 'ct-socialmeta'))->set_width(50)
    ->set_conditional_logic(array(
        array( 'field' => 'ctsm_facebook_is_default' ),
        array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' )
    )),
    Field::make('text', 'ctsm_facebook_business_country_name', __('Country', 'ct-socialmeta'))->set_width(50)
    ->set_conditional_logic(array(
        array( 'field' => 'ctsm_facebook_is_default' ),
        array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' )
    )),
    Field::make('text', 'ctsm_facebook_business_phone_number', __('Phone', 'ct-socialmeta'))->set_width(50)
    ->set_conditional_logic(array(
        array( 'field' => 'ctsm_facebook_is_default' ),
        array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' )
    )),
    Field::make('text', 'ctsm_facebook_business_website', __('Website', 'ct-socialmeta'))->set_width(50)
    ->set_conditional_logic(array(
        array( 'field' => 'ctsm_facebook_is_default' ),
        array( 'field' => 'ctsm_facebook_type', 'value' => 'business.business' )
    )),

    Field::make('separator', 'ctsm_facebook_place_desc', __('Place Coordinates', 'ct-socialmeta') . '<br /><small style="display: block;font-size: 12px;font-weight: 400;line-height: 2;">'. sprintf(__('Find your Latitute and Longitude at %s.','ct-socialmeta'), '<a href="http://www.latlong.net/" target="_blank">latlong.net</a>') .'</small>')
        ->set_conditional_logic(array(
            array( 'field' => 'ctsm_facebook_is_default' ),
            array( 'field' => 'ctsm_facebook_type', 'compare' => 'IN', 'value' => array( 'place', 'business.business' ) )
        )),
    Field::make('text', 'ctsm_facebook_place_lat', __('Location Latitude', 'ct-socialmeta'))->set_width(50)
        ->set_conditional_logic(array(
            array( 'field' => 'ctsm_facebook_is_default' ),
            array( 'field' => 'ctsm_facebook_type', 'compare' => 'IN', 'value' => array( 'place', 'business.business' ) )
        )),
    Field::make('text', 'ctsm_facebook_place_long', __('Location Longitude', 'ct-socialmeta'))->set_width(50)
        ->set_conditional_logic(array(
            array( 'field' => 'ctsm_facebook_is_default' ),
            array( 'field' => 'ctsm_facebook_type', 'compare' => 'IN', 'value' => array( 'place', 'business.business' ) )
        )),

))
;
